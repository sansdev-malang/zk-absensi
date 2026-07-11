<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\ShiftDetail;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Guru & Karyawan (Nonshift) Standard
        $guru = Shift::create(['nama' => 'Guru & Karyawan', 'kategori' => 'Guru']);
        // Senin (1) - Jumat (5)
        for ($i = 1; $i <= 5; $i++) {
            ShiftDetail::create(['shift_id' => $guru->id, 'hari' => $i, 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:30:00']);
        }
        // Sabtu (6)
        ShiftDetail::create(['shift_id' => $guru->id, 'hari' => 6, 'jam_masuk' => '07:30:00', 'jam_pulang' => '12:00:00']);

        // 2. Guru GPK/GPQ (Sabtu Libur)
        $gp = Shift::create(['nama' => 'Guru GPK & GPQ', 'kategori' => 'Guru']);
        for ($i = 1; $i <= 5; $i++) {
            ShiftDetail::create(['shift_id' => $gp->id, 'hari' => $i, 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:30:00']);
        }

        // 3. Salehmart Shift 1 (Pagi)
        $sm1 = Shift::create(['nama' => 'Salehmart Shift 1 (Pagi)', 'kategori' => 'Salehmart']);
        for ($i = 1; $i <= 5; $i++) {
            ShiftDetail::create(['shift_id' => $sm1->id, 'hari' => $i, 'jam_masuk' => '07:00:00', 'jam_pulang' => '14:00:00']);
        }
        ShiftDetail::create(['shift_id' => $sm1->id, 'hari' => 6, 'jam_masuk' => '07:30:00', 'jam_pulang' => '12:00:00']);

        // 4. Salehmart Shift 2 (Siang)
        $sm2 = Shift::create(['nama' => 'Salehmart Shift 2 (Siang)', 'kategori' => 'Salehmart']);
        for ($i = 1; $i <= 5; $i++) {
            ShiftDetail::create(['shift_id' => $sm2->id, 'hari' => $i, 'jam_masuk' => '14:00:00', 'jam_pulang' => '20:00:00']);
        }
        ShiftDetail::create(['shift_id' => $sm2->id, 'hari' => 6, 'jam_masuk' => '12:00:00', 'jam_pulang' => '18:00:00']);

        // 5. Salehmart Shift 3 (Toko Belakang)
        $sm3 = Shift::create(['nama' => 'Salehmart Shift 3 (Toko Belakang)', 'kategori' => 'Salehmart']);
        for ($i = 1; $i <= 5; $i++) {
            ShiftDetail::create(['shift_id' => $sm3->id, 'hari' => $i, 'jam_masuk' => '08:00:00', 'jam_pulang' => '15:30:00']);
        }
        ShiftDetail::create(['shift_id' => $sm3->id, 'hari' => 6, 'jam_masuk' => '07:30:00', 'jam_pulang' => '12:00:00']);

        // 6. Satpam Shift 1
        $sp1 = Shift::create(['nama' => 'Satpam Shift 1', 'kategori' => 'Satpam']);
        for ($i = 0; $i <= 6; $i++) {
            ShiftDetail::create(['shift_id' => $sp1->id, 'hari' => $i, 'jam_masuk' => '06:00:00', 'jam_pulang' => '14:00:00']);
        }

        // 7. Satpam Shift 2
        $sp2 = Shift::create(['nama' => 'Satpam Shift 2', 'kategori' => 'Satpam']);
        for ($i = 0; $i <= 6; $i++) {
            ShiftDetail::create(['shift_id' => $sp2->id, 'hari' => $i, 'jam_masuk' => '14:00:00', 'jam_pulang' => '22:00:00']);
        }

        // 8. Satpam Shift 3 (Malam - Cross Day)
        $sp3 = Shift::create(['nama' => 'Satpam Shift 3', 'kategori' => 'Satpam']);
        for ($i = 0; $i <= 6; $i++) {
            ShiftDetail::create(['shift_id' => $sp3->id, 'hari' => $i, 'jam_masuk' => '22:00:00', 'jam_pulang' => '06:00:00', 'is_cross_day' => true]);
        }
    }
}
