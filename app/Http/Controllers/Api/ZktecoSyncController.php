<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ZktecoSyncController extends Controller
{
    /**
     * Handle incoming attendance data from local sync script.
     */
    public function syncAttendance(Request $request)
    {
        $request->validate([
            'device_ip' => 'required|string',
            'attendances' => 'required|array'
        ]);

        $deviceIp = $request->input('device_ip');
        $logs = $request->input('attendances');
        
        $device = Device::where('ip_address', $deviceIp)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found on server'], 404);
        }

        $totalSynced = 0;
        
        foreach ($logs as $log) {
            $userId = $log['user_id'] ?? $log['uid'] ?? null;
            if (!$userId) continue;

            $user = User::where('uid', $userId)->first();
            
            if ($user) {
                try {
                    $waktu = Carbon::parse($log['record_time']);
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
                    }
                } catch (\Exception $e) {
                    Log::error("API Error saving attendance from device {$device->ip_address}: " . $e->getMessage());
                }
            }
        }

        $device->update(['last_sync_at' => now()]);

        return response()->json([
            'message' => 'Sync successful',
            'synced_count' => $totalSynced
        ]);
    }

    /**
     * Handle incoming users data from local sync script.
     */
    public function syncUsers(Request $request)
    {
        $request->validate([
            'device_ip' => 'required|string',
            'users' => 'required|array'
        ]);
        
        $deviceIp = $request->input('device_ip');
        $users = $request->input('users');

        $device = Device::where('ip_address', $deviceIp)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found on server'], 404);
        }

        $totalSynced = 0;

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
            }
        }

        $device->update(['last_sync_at' => now()]);

        return response()->json([
            'message' => 'Users sync successful',
            'synced_count' => $totalSynced
        ]);
    }
}
