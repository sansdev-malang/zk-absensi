<?php
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

// Konfigurasi Mesin ZKTeco (Bisa lebih dari 1 mesin)
$devices = [
    [
        'ip' => '10.10.2.26', 
        'port' => 4370,
        'com_key' => 0
    ],
    // Tambahkan mesin kedua di bawah ini dengan menghilangkan tanda //
    [
        'ip' => '10.10.2.223', 
        'port' => 4370,
        'com_key' => 123456, // Ganti dengan Comm Key asli di mesin 2
        'protocol' => 'tcp'  // Coba ganti ke 'tcp' jika 'udp' gagal
    ]
];

$apiUrl = 'https://zkabsensi.gheptech.com/api/sync'; // URL aplikasi web hosting Anda
$apiToken = 'rahasia123'; // Harus sama dengan SYNC_SECRET_TOKEN di .env server

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
