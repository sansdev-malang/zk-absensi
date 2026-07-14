<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::updateOrCreate(['key' => 'payroll_start_date'], ['value' => '27']);
        \App\Models\Setting::updateOrCreate(['key' => 'payroll_end_date'], ['value' => '26']);
    }
}
