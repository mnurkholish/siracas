<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Admin manual
        DB::table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'foto_profil' => null,
            'tanggal_lahir' => '1990-01-01',
            'nomor_hp' => '085648569562',
            'jenis_kelamin' => 'laki-laki',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        DB::table('users')->insert([
            'username' => 'Customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('00000000'),
            'foto_profil' => null,
            'tanggal_lahir' => '1990-01-01',
            'nomor_hp' => '081234567890',
            'jenis_kelamin' => 'laki-laki',
            'role' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // Customer pakai factory (misal 10 data)
        User::factory()->count(10)->create();
    }
}
