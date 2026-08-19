<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\AttendanceCalculatorService;

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
        $usersToRecalculate = [];
        
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
                        $dateString = $waktu->toDateString();
                        $usersToRecalculate[$user->id][$dateString] = [
                            'user' => $user,
                            'date' => $waktu->copy()->startOfDay()
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error("API Error saving attendance from device {$device->ip_address}: " . $e->getMessage());
                }
            }
        }

        // Recalculate daily attendance for affected users/dates
        try {
            $calculator = app(\App\Services\AttendanceCalculatorService::class);
            foreach ($usersToRecalculate as $userId => $dates) {
                foreach ($dates as $dateString => $data) {
                    try {
                        $calculator->calculateUserDaily($data['user'], $data['date']);
                    } catch (\Throwable $e) {
                        Log::error("Failed to recalculate attendance for User {$userId} on {$dateString}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("Calculator service not found or failed: " . $e->getMessage());
        }



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
                // Jangan buat user baru secara otomatis karena Web adalah Master.
                // Abaikan user dari mesin yang tidak ada di Web.
                continue;
            } else {
                $totalSynced++;
            }
        }



        return response()->json([
            'message' => 'Users sync successful',
            'synced_count' => $totalSynced
        ]);
    }

    /**
     * Get list of users to be pushed to the devices.
     */
    public function getUsersToPush(Request $request)
    {
        // Get all active users that have a UID.
        // We can optimize this later by adding a synced flag, but for now we push all active users
        // to ensure machines are up to date. Pushing users is fast.
        $users = User::whereNotNull('uid')
            ->where('uid', '!=', '')
            ->with('roles')
            ->get()
            ->map(function($user) {
                return [
                    'internal_id' => $user->id,
                    'uid' => $user->uid,
                    'name' => $user->name,
                    'password' => '', // Web hashed password cannot be used for machine, usually set empty
                    'role' => $user->hasRole('Admin') ? 14 : 0, // 14 for Super Admin, 0 for Normal User
                ];
            });

        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * Get list of active devices to be synced.
     */
    public function getDevices(Request $request)
    {
        $devices = Device::where('status', true)->get()->map(function($device) {
            return [
                'ip' => $device->ip_address,
                'port' => $device->port,
                'com_key' => (int) ($device->comm_key ?? 0),
                'protocol' => 'udp'
            ];
        });

        return response()->json([
            'devices' => $devices
        ]);
    }
}
