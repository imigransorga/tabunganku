<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun admin default. updateOrCreate -> aman dijalankan berulang.
        // Kategori default otomatis dibuat lewat event User::created.
        User::updateOrCreate(
            ['email' => 'admin@tabunganku.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin12345'),
                'email_verified_at' => now(),
            ]
        );
    }
}
