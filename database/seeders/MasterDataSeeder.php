<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BonusScheme;
use App\Models\Shift;

class MasterDataSeeder extends Seeder
{
    public function run()
    {
        $scheme = BonusScheme::updateOrCreate(['id' => 1], [
            'nama' => 'Global Skema',
            'deskripsi' => 'Aturan Bonus untuk semua karyawan',
        ]);
        $scheme->rules()->updateOrCreate(['id' => 1], [
            'min_menit' => -120,
            'max_menit' => -10,
            'nominal' => 10000.00,
        ]);
        $scheme->rules()->updateOrCreate(['id' => 2], [
            'min_menit' => -9,
            'max_menit' => 0,
            'nominal' => 8000.00,
        ]);
        $scheme->rules()->updateOrCreate(['id' => 3], [
            'min_menit' => 1,
            'max_menit' => 10,
            'nominal' => 6000.00,
        ]);
        $scheme->rules()->updateOrCreate(['id' => 4], [
            'min_menit' => 11,
            'max_menit' => 20,
            'nominal' => 4000.00,
        ]);
        $scheme->rules()->updateOrCreate(['id' => 5], [
            'min_menit' => 21,
            'max_menit' => 30,
            'nominal' => 2000.00,
        ]);
        $shift = Shift::updateOrCreate(['id' => 1], [
            'nama' => 'Guru Reguler',
            'kategori' => 'Guru Kelas dan Guru Mapel',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 1], [
            'hari' => 1,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 2], [
            'hari' => 2,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 3], [
            'hari' => 3,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 4], [
            'hari' => 4,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 5], [
            'hari' => 5,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 6], [
            'hari' => 6,
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '12:00:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 2], [
            'nama' => 'GPK & GPQ',
            'kategori' => '',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 7], [
            'hari' => 1,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 8], [
            'hari' => 2,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 9], [
            'hari' => 3,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 10], [
            'hari' => 4,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 11], [
            'hari' => 5,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '15:00:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 3], [
            'nama' => 'Tenaga Kebersihan / Dapur',
            'kategori' => '',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 18], [
            'hari' => 1,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 19], [
            'hari' => 2,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 20], [
            'hari' => 3,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 21], [
            'hari' => 4,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 22], [
            'hari' => 5,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 23], [
            'hari' => 6,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '12:30:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 4], [
            'nama' => 'Shift 1 Salehmart',
            'kategori' => 'Pagi',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 36], [
            'hari' => 1,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 37], [
            'hari' => 2,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 38], [
            'hari' => 3,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 39], [
            'hari' => 4,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 40], [
            'hari' => 5,
            'jam_masuk' => '06:30:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 41], [
            'hari' => 6,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '12:00:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 5], [
            'nama' => 'Shift 2 Salehmart',
            'kategori' => 'Siang',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 42], [
            'hari' => 1,
            'jam_masuk' => '12:00:00',
            'jam_pulang' => '20:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 43], [
            'hari' => 2,
            'jam_masuk' => '12:00:00',
            'jam_pulang' => '20:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 44], [
            'hari' => 3,
            'jam_masuk' => '12:00:00',
            'jam_pulang' => '20:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 45], [
            'hari' => 4,
            'jam_masuk' => '12:00:00',
            'jam_pulang' => '20:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 46], [
            'hari' => 5,
            'jam_masuk' => '12:00:00',
            'jam_pulang' => '20:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 47], [
            'hari' => 6,
            'jam_masuk' => '12:00:00',
            'jam_pulang' => '18:00:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 6], [
            'nama' => 'Shift 3 Salehmart',
            'kategori' => 'Toko Belakang',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 48], [
            'hari' => 1,
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 49], [
            'hari' => 2,
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 50], [
            'hari' => 3,
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 51], [
            'hari' => 4,
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 52], [
            'hari' => 5,
            'jam_masuk' => '07:30:00',
            'jam_pulang' => '15:30:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 53], [
            'hari' => 6,
            'jam_masuk' => '07:00:00',
            'jam_pulang' => '12:00:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 7], [
            'nama' => 'Shif 1 Satpam',
            'kategori' => 'Pagi',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 54], [
            'hari' => 1,
            'jam_masuk' => '06:00:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 55], [
            'hari' => 2,
            'jam_masuk' => '06:00:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 56], [
            'hari' => 3,
            'jam_masuk' => '06:00:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 57], [
            'hari' => 4,
            'jam_masuk' => '06:00:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 58], [
            'hari' => 5,
            'jam_masuk' => '06:00:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 59], [
            'hari' => 6,
            'jam_masuk' => '06:00:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 60], [
            'hari' => 0,
            'jam_masuk' => '06:00:00',
            'jam_pulang' => '14:00:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 8], [
            'nama' => 'Shift 2 Satpam',
            'kategori' => 'Siang',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 68], [
            'hari' => 1,
            'jam_masuk' => '14:00:00',
            'jam_pulang' => '22:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 69], [
            'hari' => 2,
            'jam_masuk' => '14:00:00',
            'jam_pulang' => '22:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 70], [
            'hari' => 3,
            'jam_masuk' => '14:00:00',
            'jam_pulang' => '22:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 71], [
            'hari' => 4,
            'jam_masuk' => '14:00:00',
            'jam_pulang' => '22:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 72], [
            'hari' => 5,
            'jam_masuk' => '14:00:00',
            'jam_pulang' => '22:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 73], [
            'hari' => 6,
            'jam_masuk' => '14:00:00',
            'jam_pulang' => '22:00:00',
            'is_cross_day' => 0,
        ]);
        $shift->details()->updateOrCreate(['id' => 74], [
            'hari' => 0,
            'jam_masuk' => '14:00:00',
            'jam_pulang' => '22:00:00',
            'is_cross_day' => 0,
        ]);
        $shift = Shift::updateOrCreate(['id' => 9], [
            'nama' => 'Shift 3 Satpam',
            'kategori' => 'Malam',
            'bonus_scheme_id' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 75], [
            'hari' => 1,
            'jam_masuk' => '22:00:00',
            'jam_pulang' => '06:00:00',
            'is_cross_day' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 76], [
            'hari' => 2,
            'jam_masuk' => '22:00:00',
            'jam_pulang' => '06:00:00',
            'is_cross_day' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 77], [
            'hari' => 3,
            'jam_masuk' => '22:00:00',
            'jam_pulang' => '06:00:00',
            'is_cross_day' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 78], [
            'hari' => 4,
            'jam_masuk' => '22:00:00',
            'jam_pulang' => '06:00:00',
            'is_cross_day' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 79], [
            'hari' => 5,
            'jam_masuk' => '22:00:00',
            'jam_pulang' => '06:00:00',
            'is_cross_day' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 80], [
            'hari' => 6,
            'jam_masuk' => '22:00:00',
            'jam_pulang' => '06:00:00',
            'is_cross_day' => 1,
        ]);
        $shift->details()->updateOrCreate(['id' => 81], [
            'hari' => 0,
            'jam_masuk' => '22:00:00',
            'jam_pulang' => '06:00:00',
            'is_cross_day' => 1,
        ]);
    }
}
