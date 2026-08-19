<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if name is empty
        if (!isset($row['name']) || empty(trim($row['name']))) {
            return null;
        }

        $email = $row['email'] ?? ($row['uid'] ? $row['uid'] . '@example.com' : fake()->unique()->safeEmail());
        $password = $row['password'] ?? 'password123';

        $userData = [
            'name'     => $row['name'],
            'email'    => $email,
            'password' => Hash::make($password),
            'jabatan'  => $row['jabatan'] ?? null,
        ];

        // Ensure UID is set properly if provided
        if (isset($row['uid']) && !empty(trim($row['uid']))) {
            $userData['uid'] = trim($row['uid']);
        }

        // Try to update existing by UID or Email, otherwise create manually to avoid deadlocks
        $user = null;
        if (isset($userData['uid'])) {
            $user = User::where('uid', $userData['uid'])->first();
        } elseif (isset($userData['email'])) {
            $user = User::where('email', $userData['email'])->first();
        }

        if ($user) {
            $user->update($userData);
        } else {
            $user = User::create($userData);
        }

        $roleName = $row['role'] ?? 'User';
        if (!$user->hasRole($roleName)) {
            $user->assignRole($roleName);
        }

        return $user;
    }
}
