<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BonusScheme;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class BonusSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan dulu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BonusScheme::truncate();
        DB::table('bonus_rules')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- SKEMA 1: GURU & KARYAWAN ---
        $schemeGuru = BonusScheme::create([
            'nama' => 'Bonus Guru & Karyawan',
            'deskripsi' => 'Skema bonus utama untuk pendidik dan staff non-shift.',
        ]);

        $rulesGuru = [
            ['min_menit' => -999, 'max_menit' => -10, 'nominal' => 10000],
            ['min_menit' => -9, 'max_menit' => 0, 'nominal' => 8000],
            ['min_menit' => 1, 'max_menit' => 10, 'nominal' => 6000],
            ['min_menit' => 11, 'max_menit' => 20, 'nominal' => 4000],
            ['min_menit' => 21, 'max_menit' => 30, 'nominal' => 2000],
            ['min_menit' => 31, 'max_menit' => 999, 'nominal' => 0],
        ];

        foreach ($rulesGuru as $rule) {
            $schemeGuru->rules()->create($rule);
        }

        Shift::where('nama', 'Guru & Karyawan (Nonshift)')->update(['bonus_scheme_id' => $schemeGuru->id]);


        // --- SKEMA 2: SALEHMART ---
        $schemeSalehmart = BonusScheme::create([
            'nama' => 'Bonus Salehmart',
            'deskripsi' => 'Skema bonus untuk pegawai toko Salehmart (semua shift).',
        ]);

        $rulesSalehmart = [
            ['min_menit' => -999, 'max_menit' => 0, 'nominal' => 8000], // Datang lebih awal / tepat waktu
            ['min_menit' => 1, 'max_menit' => 15, 'nominal' => 5000],  // Telat dikit
            ['min_menit' => 16, 'max_menit' => 999, 'nominal' => 0],   // Telat banyak
        ];

        foreach ($rulesSalehmart as $rule) {
            $schemeSalehmart->rules()->create($rule);
        }

        Shift::where('nama', 'LIKE', 'Salehmart%')->update(['bonus_scheme_id' => $schemeSalehmart->id]);


        // --- SKEMA 3: SATPAM ---
        $schemeSatpam = BonusScheme::create([
            'nama' => 'Bonus Satpam',
            'deskripsi' => 'Skema bonus khusus keamanan. Nominal disesuaikan.',
        ]);

        $rulesSatpam = [
            ['min_menit' => -999, 'max_menit' => 0, 'nominal' => 10000], // Tepat waktu / Awal
            ['min_menit' => 1, 'max_menit' => 999, 'nominal' => 0],      // Telat langsung hangus
        ];

        foreach ($rulesSatpam as $rule) {
            $schemeSatpam->rules()->create($rule);
        }

        Shift::where('nama', 'LIKE', 'Satpam%')->update(['bonus_scheme_id' => $schemeSatpam->id]);
    }
}
