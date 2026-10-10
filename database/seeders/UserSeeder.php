<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tài khoản Admin / Quản trị viên
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản Trị Viên (Admin)',
                'password' => Hash::make('tieudoi2p'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Tài khoản Thủ thư / Librarian
        User::updateOrCreate(
            ['email' => 'thuthu@gmail.com'],
            [
                'name' => 'Thủ Thư Thư Viện',
                'password' => Hash::make('tieudoi2p'),
                'role' => 'librarian',
                'email_verified_at' => now(),
            ]
        );

        // 3. Tài khoản Độc giả / Reader
        User::updateOrCreate(
            ['email' => 'docgia@gmail.com'],
            [
                'name' => 'Độc Giả Mẫu',
                'password' => Hash::make('tieudoi2p'),
                'role' => 'reader',
                'email_verified_at' => now(),
            ]
        );
    }
}
