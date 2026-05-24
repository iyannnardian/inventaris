<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::firstOrCreate(
            ['email' => 'admin@sppg.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Akun Ahli Gizi
        User::firstOrCreate(
            ['email' => 'ahligizi@sppg.com'],
            [
                'name' => 'Ahli Gizi SPPG',
                'password' => Hash::make('password123'),
                'role' => 'ahli gizi',
            ]
        );

        // Akun Kepala Dapur
        User::firstOrCreate(
            ['email' => 'dapur@sppg.com'],
            [
                'name' => 'Kepala Dapur',
                'password' => Hash::make('password123'),
                'role' => 'kepala dapur',
            ]
        );

        // Kategori Default
        $kategoris = [
            'Karbohidrat',
            'Protein',
            'Sayuran dan Buah-buahan',
            'Bahan Baku Lainnya',
        ];

        foreach ($kategoris as $nama) {
            \App\Models\Kategori::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}