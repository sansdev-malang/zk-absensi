<?php
// === SYNC DIPAUSE SEMENTARA ===
echo "Proses sinkronisasi sedang dihentikan sementara (PAUSED).\n";
exit;
// ================================================

require 'vendor/autoload.php';

require_once 'RobustZkteco.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$lockFile = __DIR__ . '/sync.lock';
if (file_exists($lockFile)) {
    // Cek jika lock file umurnya lebih dari 15 menit (stale lock), hapus
    if (time() - filemtime($lockFile) > 900) {
        unlink($lockFile);
    } else {
        echo "Proses sinkronisasi sedang berjalan. Menunggu antrean berikutnya...\n";
        exit;
    }
}
// Buat lock file
file_put_contents($lockFile, 'locked');

// Ambil Data Konfigurasi Mesin ZKTeco dari Web
$devices = [];
try {
    // Karena kita butuh memanggil API sebelum loop berjalan, 
    // kita inisiasi Client HTTP dasar sementara untuk get devices (karena auth di bawah)
    $apiUrl = 'https://zkabsensi.gheptech.com/api/sync'; // URL aplikasi web hosting Anda
    $apiToken = 'rahasia123'; // Harus sama dengan SYNC_SECRET_TOKEN di .env server
    
    $tempClient = new Client([
        'base_uri' => $apiUrl . '/',
        'headers' => [
            'Authorization' => 'Bearer ' . $apiToken,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ],
        'verify' => false,
    ]);
    
    $res = $tempClient->get('devices');
    $resData = json_decode($res->getBody(), true);
    if (!empty($resData['devices'])) {
        $devices = $resData['devices'];
        echo "-> Mendapatkan " . count($devices) . " data mesin aktif dari server.\n";
    } else {
        echo "-> Tidak ada mesin yang berstatus aktif di server untuk disinkronisasi.\n";
        unlink($lockFile);
        exit;
    }
} catch (\Exception $e) {
    echo "-> [Error] Gagal mengambil data konfigurasi mesin dari server: " . $e->getMessage() . "\n";
    unlink($lockFile);
    exit;
}

// Inisialisasi HTTP Client API
$client = new Client([
    'base_uri' => $apiUrl . '/',
    'headers' => [
        'Authorization' => 'Bearer ' . $apiToken,
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
    ],
    'verify' => false, // Set true jika SSL di server sudah valid, false untuk skip SSL check (localhost/IP)
]);

// Ambil data users dari server untuk dipush ke mesin
$usersToPush = [];
try {
    $response = $client->get('users-to-push');
    $resData = json_decode($response->getBody(), true);
    if (!empty($resData['users'])) {
        $usersToPush = $resData['users'];
        echo "-> Mendapatkan " . count($usersToPush) . " data user dari server untuk dipush ke mesin.\n";
    }
} catch (\Exception $e) {
    echo "-> [Warning] Gagal mengambil data user dari server untuk dipush: " . $e->getMessage() . "\n";
}

// 1. Mulai Looping untuk setiap mesin
foreach ($devices as $device) {
    $deviceIp = $device['ip'];
    $devicePort = $device['port'] ?? 4370;
    $deviceComKey = $device['com_key'] ?? 0;
    $deviceProtocol = $device['protocol'] ?? 'udp';

    echo "==========================================\n";
    echo "Memulai sinkronisasi untuk mesin IP: $deviceIp\n";
    
    try {
        $zk = new RobustZkteco($deviceIp, $devicePort, $deviceComKey, $deviceProtocol);

        // 2. Ambil Data
        $attendances = $zk->getAttendance();
        $users = $zk->getUsers();
        
        // Filter data absensi agar hanya mengirim data yang lebih baru dari sinkronisasi terakhir
        $syncCacheFile = "last_sync_" . str_replace('.', '_', $deviceIp) . ".txt";
        $lastSyncTime = file_exists($syncCacheFile) ? trim(file_get_contents($syncCacheFile)) : "2000-01-01 00:00:00";
        $newAttendances = [];
        $latestRecordTime = $lastSyncTime;
        
        foreach ($attendances as $att) {
            if ($att['record_time'] > $lastSyncTime) {
                $newAttendances[] = $att;
                if ($att['record_time'] > $latestRecordTime) {
                    $latestRecordTime = $att['record_time'];
                }
            }
        }
        $attendances = $newAttendances;

        echo "-> Berhasil mengambil " . count($attendances) . " data absensi BARU (dari total memori) dan " . count($users) . " data user.\n";

        // Sinkronisasi User
        if (!empty($users)) {
            $userChunks = array_chunk($users, 100);
            foreach ($userChunks as $i => $chunk) {
                echo "-> Mengirim data user ke server (Part " . ($i + 1) . " dari " . count($userChunks) . ")...\n";
                $response = $client->post('users', [
                    'json' => [
                        'device_ip' => $deviceIp,
                        'users' => $chunk
                    ]
                ]);
                echo "   Response User: " . $response->getBody() . "\n";
            }
        }

        // Mapping user yang sudah ada di mesin berdasarkan PIN (user_id)
        $machineUsersByPin = [];
        $uidsToDelete = []; // Kumpulkan UID yang akan dihapus, termasuk duplikat internal
        foreach ($users as $u) {
            $pinKey = trim((string)($u['user_id'] ?? ''));
            if ($pinKey !== '') {
                if (isset($machineUsersByPin[$pinKey])) {
                    // Ditemukan duplikat PIN di dalam memori mesin!
                    // Kita pertahankan yang pertama, dan tandai yang ini untuk dihapus.
                    $uidsToDelete[] = $u['uid'];
                } else {
                    $machineUsersByPin[$pinKey] = $u;
                }
            }
        }

        // Push Users ke Mesin
        if (!empty($usersToPush)) {
            echo "-> Mengecek " . count($usersToPush) . " data user dari server untuk dikirim ke mesin...\n";
            $batchUsers = [];
            $serverPins = [];
            
            foreach ($usersToPush as $serverUser) {
                $pin = (string)($serverUser['uid'] ?? '');
                $pin = trim($pin);
                if ($pin === '') continue;
                
                $serverPins[$pin] = true;

                $internalUid = $serverUser['internal_id'];
                $password = $serverUser['password'] ?? '';
                $role = $serverUser['role'] ?? 0;
                $name = substr($serverUser['name'], 0, 24);

                // Jika user sudah ada di mesin, JAGA UID INTERNAL agar sidik jari tidak hilang!
                if (isset($machineUsersByPin[$pin])) {
                    $existing = $machineUsersByPin[$pin];
                    $internalUid = $existing['uid']; // Wajib pakai UID asli mesin
                    
                    // [PENTING] Jangan turunkan pangkat Admin!
                    if ($existing['role'] > 0 && $role == 0) {
                        $role = $existing['role'];
                    }

                    // Skip jika nama, role, dan password sudah sama persis (hemat waktu)
                    if (trim(str_replace(chr(0), '', $existing['name'])) === trim($name) && $existing['role'] == $role && (empty($password) || $existing['password'] === $password)) {
                        continue; 
                    }

                    // Pertahankan password lama di mesin jika web tidak mengirim password baru
                    if (empty($password) && !empty($existing['password'])) {
                        $password = $existing['password'];
                    }
                }

                $batchUsers[] = [
                    'uid' => $internalUid,
                    'userid' => $pin,
                    'name' => $name,
                    'password' => $password,
                    'role' => $role
                ];
            }
            
            if (!empty($batchUsers)) {
                $pushedCount = $zk->setUsers($batchUsers);
                echo "   Berhasil menyuntikkan/mengupdate $pushedCount user ke mesin.\n";
            } else {
                echo "   Tidak ada user yang perlu diupdate (sudah tersinkronisasi).\n";
            }
            
            // Hapus user di mesin yang TIDAK ADA di server (Enforce Web as Master)
            foreach ($machineUsersByPin as $machinePin => $machineUser) {
                if (!isset($serverPins[$machinePin])) {
                    $uidsToDelete[] = $machineUser['uid'];
                }
            }
            
            if (!empty($uidsToDelete)) {
                echo "-> Ditemukan " . count($uidsToDelete) . " user di mesin yang sudah dihapus di Web. Menghapus dari mesin...\n";
                $deletedCount = $zk->deleteUsers($uidsToDelete);
                echo "   Berhasil menghapus $deletedCount user dari mesin.\n";
            }
        }

        // Sinkronisasi Absensi
        if (!empty($attendances)) {
            $attChunks = array_chunk($attendances, 1000);
            foreach ($attChunks as $i => $chunk) {
                echo "-> Mengirim data absensi ke server (Part " . ($i + 1) . " dari " . count($attChunks) . ")...\n";
                $response = $client->post('attendance', [
                    'json' => [
                        'device_ip' => $deviceIp,
                        'attendances' => $chunk
                    ]
                ]);
                echo "   Response Absensi: " . $response->getBody() . "\n";
            }
            
            // Simpan waktu record terakhir ke file cache agar sinkronisasi berikutnya tidak mengirim ulang
            file_put_contents($syncCacheFile, $latestRecordTime);
            
            // ====================================================================================
            // [OPSI HAPUS LOG DI MESIN]
            // Hapus dua garis miring (//) pada 3 baris di bawah ini HANYA JIKA Anda ingin
            // mengosongkan seluruh memori absensi di mesin fisik (misal saat akhir bulan/periode).
            // ====================================================================================
            // $zk->clearAttendance();
            // unlink($syncCacheFile); // Hapus cache lokal agar sinkronisasi bulan depan mulai dari awal
            // echo "   *** LOG ABSENSI DI MESIN FISIK BERHASIL DIKOSONGKAN! ***\n";
        }

    } catch (RequestException $e) {
        echo "-> [Error API] Gagal mengirim ke server untuk mesin $deviceIp: \n";
        if ($e->hasResponse()) {
            echo "   " . $e->getResponse()->getBody() . "\n";
        } else {
            echo "   " . $e->getMessage() . "\n";
        }
    } catch (\Exception $e) {
        echo "-> [Error Mesin] Gagal terhubung ke mesin ZKTeco $deviceIp: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "Sinkronisasi selesai untuk semua mesin.\n";

if (file_exists($lockFile)) {
    unlink($lockFile);
}
