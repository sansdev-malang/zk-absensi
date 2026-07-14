<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\UserShift;
use Carbon\Carbon;

class RawAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('email', 'like', 'karyawan%@example.com')->get();
        if ($users->isEmpty()) return;

        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->startOfDay();

        $this->command->info("Mensimulasikan Raw Attendances (Mesin) dari {$startDate->toDateString()} s/d {$endDate->toDateString()}...");

        // Hapus data lama agar clean
        $userIds = $users->pluck('id')->toArray();
        Attendance::whereIn('user_id', $userIds)->delete();

        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            foreach ($users as $user) {
                $roster = UserShift::with('shift.details')
                                   ->where('user_id', $user->id)
                                   ->where('tanggal', $currentDate->toDateString())
                                   ->first();
                
                if (!$roster || !$roster->shift) {
                    continue;
                }
                
                $shiftDetail = $roster->shift->details->where('hari', $currentDate->dayOfWeek)->first();

                // Libur
                if (!$shiftDetail) {
                    continue;
                }

                // 5% peluang absen
                if (rand(1, 100) <= 5) {
                    continue;
                }

                // Jam Masuk
                $targetMasuk = Carbon::parse($currentDate->toDateString() . ' ' . $shiftDetail->jam_masuk);
                $randMasuk = rand(1, 100);
                
                if ($randMasuk <= 60) {
                    $waktuMasuk = $targetMasuk->copy()->subMinutes(rand(10, 45));
                } elseif ($randMasuk <= 90) {
                    $waktuMasuk = $targetMasuk->copy()->addMinutes(rand(1, 15));
                } else {
                    $waktuMasuk = $targetMasuk->copy()->addMinutes(rand(16, 60));
                }

                Attendance::create([
                    'user_id' => $user->id,
                    'status' => 0, // masuk
                    'waktu' => $waktuMasuk,
                ]);

                // Jam Pulang
                $targetPulangDate = $shiftDetail->is_cross_day ? $currentDate->copy()->addDay() : $currentDate->copy();
                $targetPulang = $targetPulangDate->setTimeFromTimeString($shiftDetail->jam_pulang);

                $randPulang = rand(1, 100);
                if ($randPulang <= 70) {
                    $waktuPulang = $targetPulang->copy()->addMinutes(rand(0, 30));
                } elseif ($randPulang <= 90) {
                    $waktuPulang = $targetPulang->copy()->subMinutes(rand(1, 30));
                } else {
                    $waktuPulang = $targetPulang->copy()->addMinutes(rand(60, 180));
                }

                Attendance::create([
                    'user_id' => $user->id,
                    'status' => 1, // pulang
                    'waktu' => $waktuPulang,
                ]);
            }
            $currentDate->addDay();
        }
        
        $this->command->info("RawAttendanceSeeder selesai.");
    }
}
