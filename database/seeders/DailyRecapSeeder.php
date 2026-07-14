<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\AttendanceCalculatorService;
use Carbon\Carbon;

class DailyRecapSeeder extends Seeder
{
    public function run(AttendanceCalculatorService $calculator): void
    {
        $users = User::where('email', 'like', 'karyawan%@example.com')->get();
        if ($users->isEmpty()) return;

        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->startOfDay();

        $this->command->info("Menghitung Rekap Harian & Bonus dari {$startDate->toDateString()} s/d {$endDate->toDateString()}...");

        // Hapus rekap lama
        $userIds = $users->pluck('id')->toArray();
        \App\Models\DailyAttendance::whereIn('user_id', $userIds)->delete();

        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            foreach ($users as $user) {
                $calculator->calculateUserDaily($user, $currentDate);
            }
            $currentDate->addDay();
        }
        
        $this->command->info("DailyRecapSeeder selesai.");
    }
}
