<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $hrRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'HR']);
        $supervisorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Supervisor']);
        $userRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'User']);

        // Create a default admin user
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Assign Admin role to the admin user
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        // Create 10 Dummy Users (Commented out for real machine testing)
        // for ($i = 1; $i <= 10; $i++) {
        //     $user = \App\Models\User::firstOrCreate(
        //         ['email' => "karyawan{$i}@example.com"],
        //         [
        //             'name' => "Karyawan {$i}",
        //             'password' => \Illuminate\Support\Facades\Hash::make('password'),
        //         ]
        //     );
        //     if (!$user->hasRole('User')) {
        //         $user->assignRole('User');
        //     }
        // }
    }
}
