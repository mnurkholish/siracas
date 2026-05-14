<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private const DEFAULT_ADMIN_PASSWORD = 'Admin123!';
    private const DEFAULT_CUSTOMER_PASSWORD = 'Customer123!';
    private const DEFAULT_REVIEW_PASSWORD = 'Review123!';

    public const DEMO_CUSTOMER_EMAILS = [
        'customer@gmail.com',
        'kholis.review@gmail.com',
        'lian.review@gmail.com',
        'thalia.review@gmail.com',
        'bilqis.review@gmail.com',
        'budi.review@gmail.com',
        'sugeng.review@gmail.com',
    ];

    public function run(): void
    {
        $users = [
            [
                'username' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make(self::DEFAULT_ADMIN_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1990-01-01',
                'nomor_hp' => '085648569562',
                'jenis_kelamin' => 'perempuan',
                'role' => 'admin',
            ],
            [
                'username' => 'Customer',
                'email' => 'customer@gmail.com',
                'password' => Hash::make(self::DEFAULT_CUSTOMER_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1990-01-01',
                'nomor_hp' => '081234567890',
                'jenis_kelamin' => 'laki-laki',
                'role' => 'customer',
            ],
            [
                'username' => 'Kholis',
                'email' => 'kholis.review@gmail.com',
                'password' => Hash::make(self::DEFAULT_REVIEW_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1992-02-12',
                'nomor_hp' => '081234567891',
                'jenis_kelamin' => 'laki-laki',
                'role' => 'customer',
            ],
            [
                'username' => 'Lian',
                'email' => 'lian.review@gmail.com',
                'password' => Hash::make(self::DEFAULT_REVIEW_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1993-03-13',
                'nomor_hp' => '081234567892',
                'jenis_kelamin' => 'laki-laki',
                'role' => 'customer',
            ],
            [
                'username' => 'Thalia',
                'email' => 'thalia.review@gmail.com',
                'password' => Hash::make(self::DEFAULT_REVIEW_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1994-04-14',
                'nomor_hp' => '081234567893',
                'jenis_kelamin' => 'perempuan',
                'role' => 'customer',
            ],
            [
                'username' => 'Bilqis',
                'email' => 'bilqis.review@gmail.com',
                'password' => Hash::make(self::DEFAULT_REVIEW_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1995-05-15',
                'nomor_hp' => '081234567894',
                'jenis_kelamin' => 'perempuan',
                'role' => 'customer',
            ],
            [
                'username' => 'Budi',
                'email' => 'budi.review@gmail.com',
                'password' => Hash::make(self::DEFAULT_REVIEW_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1991-06-16',
                'nomor_hp' => '081234567895',
                'jenis_kelamin' => 'laki-laki',
                'role' => 'customer',
            ],
            [
                'username' => 'Sugeng',
                'email' => 'sugeng.review@gmail.com',
                'password' => Hash::make(self::DEFAULT_REVIEW_PASSWORD),
                'foto_profil' => null,
                'tanggal_lahir' => '1990-07-17',
                'nomor_hp' => '081234567896',
                'jenis_kelamin' => 'laki-laki',
                'role' => 'customer',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
