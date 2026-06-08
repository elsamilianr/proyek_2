<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['username' => 'karyawan'], [
            'nama'     => 'Karyawan Toko',
            'email'    => 'karyawan@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'karyawan',
            'no_hp'    => '082345678901',
        ]);
    }
}