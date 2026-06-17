<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('user bisa login dengan data valid', function () {
    $customer = customerUser(['password' => Hash::make(validPassword())]);

    $this->post(route('login'), [
        'email' => $customer->email,
        'password' => validPassword(),
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($customer);
    $this->assertDatabaseHas('carts', ['user_id' => $customer->id]);
});

it('admin bisa login dengan data valid', function () {
    $admin = adminUser(['password' => Hash::make(validPassword())]);

    $this->post(route('login'), [
        'email' => $admin->email,
        'password' => validPassword(),
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs($admin);
});

it('login gagal jika email atau password salah', function () {
    $customer = customerUser(['password' => Hash::make(validPassword())]);

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => $customer->email,
            'password' => 'PasswordSalah123!',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('password');

    $this->assertGuest();
});

it('login gagal jika form kosong', function () {
    $this->from(route('login'))
        ->post(route('login'), [])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email', 'password']);
});

it('customer nonaktif tidak bisa login', function () {
    $customer = customerUser([
        'is_active' => false,
        'password' => Hash::make(validPassword()),
    ]);

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => $customer->email,
            'password' => validPassword(),
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('admin dan customer bisa logout lalu tidak bisa mengakses route login-required', function () {
    $customer = customerUser();

    $this->actingAs($customer)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});
