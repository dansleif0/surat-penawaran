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
        // Cek apakah user admin utama sudah ada
        if (!User::where('username', 'admin')->exists()) {
            User::create([
                'name' => 'Admin Tasniem',
                'username' => 'managerkda',           // Username untuk login
                'email' => 'admin@tasniem.com',  // Email (tetap diisi untuk kebutuhan sistem/notifikasi)
                'password' => Hash::make('123456'), // Password
            ]);
        }

        // Contoh user kedua (Opsional)
        if (!User::where('username', 'staff')->exists()) {
            User::create([
                'name' => 'Staff Gudang',
                'username' => 'staff',
                'email' => 'staff@tasniem.com',
                'password' => Hash::make('staff123'),
            ]);
        }
    }
}