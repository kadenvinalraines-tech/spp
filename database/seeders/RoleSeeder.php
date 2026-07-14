<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Tata Usaha',
            'Kepala Sekolah',
            'Yayasan',
            'Orang Tua'
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            $slug = strtolower(str_replace(' ', '', $roleName));
            
            $user = User::firstOrCreate(
                ['email' => $slug . '@sekolah.com'],
                [
                    'name' => 'User ' . $roleName,
                    'password' => Hash::make('password')
                ]
            );

            // Attach role if not attached
            if (!$user->hasRole($roleName)) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
