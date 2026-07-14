<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shift;
use App\Models\UserShift;
use Carbon\Carbon;

class RosterSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = Shift::all();
        if ($shifts->isEmpty()) {
            $this->command->warn("Tidak ada Shift yang ditemukan.");
            return;
        }

        // Ambil 10 karyawan dummy
        $users = User::where('email', 'like', 'karyawan%@example.com')->get();

        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->startOfDay();

        $this->command->info("Mengisi tabel Roster (UserShift) dari {$startDate->toDateString()} s/d {$endDate->toDateString()}...");

        // Tetapkan 1 shift random permanen per karyawan selama 30 hari
        // agar konsisten.
        foreach ($users as $user) {
            $randomShift = $shifts->random();
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                UserShift::updateOrCreate(
                    ['user_id' => $user->id, 'tanggal' => $currentDate->toDateString()],
                    ['shift_id' => $randomShift->id]
                );
                $currentDate->addDay();
            }
        }
        
        $this->command->info("RosterSeeder selesai.");
    }
}
