<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\User;
use App\Models\Attendance;
use App\Services\ZktecoService;
use App\Services\AttendanceCalculatorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ZktecoController extends Controller
{
    public function syncAttendance(Request $request, AttendanceCalculatorService $calculator)
    {
        // Menghindari Maximum execution time of 30 seconds exceeded
        set_time_limit(0);
        // Memastikan proses tetap berlanjut meskipun Nginx memutus koneksi (504 Timeout)
        ignore_user_abort(true);

        $devices = Device::where('status', true)->get();
        
        if ($devices->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada perangkat ZKTeco yang aktif untuk disinkronisasi.');
        }

        $totalSynced = 0;
        $failedDevices = [];
        $affectedCalculations = []; // Menyimpan [user_id => ['Y-m-d', 'Y-m-d']]

        foreach ($devices as $device) {
            $zkteco = new ZktecoService($device->ip_address, $device->port, $device->comm_key);
            
            if ($zkteco->connect()) {
                $logs = $zkteco->getAttendance();
                
                foreach ($logs as $log) {
                    // Cek user by UID mesin (sekarang ZKTeco TCP me-return array 'user_id' untuk PIN/ID Karyawan, dan 'uid' untuk auto-increment UID mesin)
                    $user = User::where('uid', $log['user_id'] ?? $log['uid'])->first();
                    
                    if ($user) {
                        try {
                            $waktu = Carbon::parse($log['record_time']);
                            $dateStr = $waktu->toDateString();
                            
                            if ($request->filled('start_date') && $dateStr < $request->start_date) {
                                continue;
                            }
                            if ($request->filled('end_date') && $dateStr > $request->end_date) {
                                continue;
                            }
                            
                            $state = $log['state'] ?? '0';
                            
                            $attendance = Attendance::firstOrCreate([
                                'user_id' => $user->id,
                                'waktu' => $waktu->format('Y-m-d H:i:s'),
                                'uid_log' => $log['uid'] ?? null,
                            ], [
                                'device_id' => $device->id,
                                'status' => (string)$state,
                            ]);
                            
                            if ($attendance->wasRecentlyCreated) {
                                $totalSynced++;
                                // Catat user & tanggal yang perlu di-recalc
                                $affectedCalculations[$user->id][$dateStr] = true;
                                
                                // Jika shift 3, log jam 01.00 pagi itu sebenarnya untuk shift hari sebelumnya.
                                // Kita re-calc hari sebelumnya juga agar aman.
                                $yesterdayStr = $waktu->copy()->subDay()->toDateString();
                                $affectedCalculations[$user->id][$yesterdayStr] = true;
                            }
                        } catch (\Exception $e) {
                            Log::error("Error saving attendance from device {$device->ip_address}: " . $e->getMessage());
                        }
                    }
                }
            } else {
                $failedDevices[] = $device->nama_mesin . ' (' . $device->ip_address . ')';
            }
        }

        if (count($failedDevices) > 0) {
            $failedMsg = implode(', ', $failedDevices);
            return redirect()->back()->with('warning', "Sinkronisasi selesai. Berhasil menarik $totalSynced data baru. Namun gagal terhubung ke: {$failedMsg}");
        }

        return redirect()->back()->with('success', "Sinkronisasi sukses! Berhasil menarik $totalSynced data absensi baru.");
    }

    public function syncUsers()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $devices = Device::where('status', true)->get();
        
        if ($devices->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada perangkat ZKTeco yang aktif.');
        }

        $totalSynced = 0;
        $failedDevices = [];

        foreach ($devices as $device) {
            $zkteco = new ZktecoService($device->ip_address, $device->port, $device->comm_key);
            
            if ($zkteco->connect()) {
                $users = $zkteco->getUsers();
                
                foreach ($users as $userId => $userData) {
                    $pin = $userId;
                    if (empty($pin)) {
                        $pin = $userData['uid'] ?? null;
                    }
                    if (!$pin) continue;

                    $existing = User::where('uid', $pin)->first();
                    
                    if (!$existing) {
                        // Buat user baru
                        $email = strtolower(str_replace(' ', '', $pin)) . '@example.com';
                        // Cek email unique
                        if (User::where('email', $email)->exists()) {
                            $email = strtolower(str_replace(' ', '', $pin)) . '_' . uniqid() . '@example.com';
                        }

                        $user = User::create([
                            'name' => $userData['name'] ?: 'Pegawai ' . $pin,
                            'email' => $email,
                            'password' => bcrypt('12345678'),
                            'uid' => $pin,
                        ]);
                        $user->assignRole('User');
                        $totalSynced++;
                    } else {
                        // Opsional: update nama jika ada perubahan (saat ini skip dulu agar tidak menimpa nama yang diedit manual di web)
                    }
                }
            } else {
                $failedDevices[] = $device->nama_mesin . ' (' . $device->ip_address . ')';
            }
        }

        if (count($failedDevices) > 0) {
            $failedMsg = implode(', ', $failedDevices);
            return redirect()->back()->with('warning', "Sinkronisasi selesai. Berhasil menarik $totalSynced karyawan baru. Namun gagal terhubung ke: {$failedMsg}");
        }

        return redirect()->back()->with('success', "Sinkronisasi sukses! Berhasil mengimpor $totalSynced karyawan baru dari mesin.");
    }
}
