<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin Utama
        User::updateOrCreate(
            ['username' => 'managerkda'],
            [
                'name' => 'Admin Tasniem',
                'role' => 'admin',
                'email' => 'admin@tasniem.com',
                'password' => Hash::make('123456'),
            ]
        );

        // 2. Staff Gudang (Indra)
        User::updateOrCreate(
            ['username' => 'Indra'],
            [
                'name' => 'Indra',
                'role' => 'staff_gudang',
                'email' => 'indra@tasniem.com',
                'password' => Hash::make('123456'),
            ]
        );

        // 3. Driver
        User::updateOrCreate(
            ['username' => 'driver'],
            [
                'name' => 'Driver',
                'role' => 'driver',
                'email' => 'driver@tasniem.com',
                'password' => Hash::make('123456'),
            ]
        );

        // 4. Staff Gudang (staff lama)
        User::updateOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staff Gudang',
                'role' => 'staff_gudang',
                'email' => 'staff@tasniem.com',
                'password' => Hash::make('staff123'),
            ]
        );
    }
}