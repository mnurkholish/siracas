<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer bisa membuat akun dengan data valid', function () {
    $this->post(route('register'), [
        'username' => 'customerbaru',
        'email' => 'customerbaru@example.test',
        'nomor_hp' => '081234567891',
        'password' => validPassword(),
        'jenis_kelamin' => 'laki-laki',
        'tanggal_lahir' => now()->subYears(20)->toDateString(),
    ])->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'username' => 'customerbaru',
        'email' => 'customerbaru@example.test',
        'role' => 'customer',
    ]);
});

it('register gagal jika data kosong', function () {
    $this->from(route('register'))
        ->post(route('register'), [])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['username', 'email', 'nomor_hp', 'password', 'jenis_kelamin']);
});

it('register gagal jika data tidak valid', function () {
    $this->from(route('register'))
        ->post(route('register'), [
            'username' => 'ab',
            'email' => 'bukan-email',
            'nomor_hp' => '628123',
            'password' => 'password',
            'jenis_kelamin' => 'lainnya',
            'tanggal_lahir' => now()->subYears(10)->toDateString(),
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['email', 'nomor_hp', 'password', 'jenis_kelamin', 'tanggal_lahir']);
});

it('register gagal jika email sudah digunakan', function () {
    $customer = customerUser();

    $this->from(route('register'))
        ->post(route('register'), [
            'username' => 'customerlain',
            'email' => $customer->email,
            'nomor_hp' => '081234567892',
            'password' => validPassword(),
            'jenis_kelamin' => 'perempuan',
            'tanggal_lahir' => now()->subYears(20)->toDateString(),
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors('email');
});
