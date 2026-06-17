<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('user bisa mengubah password dengan password lama valid', function () {
    $customer = customerUser(['password' => Hash::make('password')]);

    $this->actingAs($customer)
        ->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => validPassword(),
            'password_confirmation' => validPassword(),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Hash::check(validPassword(), $customer->fresh()->password))->toBeTrue();
});

it('admin bisa mengubah password dengan password lama valid', function () {
    $admin = adminUser(['password' => Hash::make('password')]);

    $this->actingAs($admin)
        ->put(route('admin.profile.password'), [
            'current_password' => 'password',
            'password' => validPassword(),
            'password_confirmation' => validPassword(),
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('gagal mengubah password jika password lama salah', function () {
    $customer = customerUser(['password' => Hash::make('password')]);

    $this->actingAs($customer)
        ->from(route('profile'))
        ->put(route('profile.password'), [
            'current_password' => 'salah',
            'password' => validPassword(),
            'password_confirmation' => validPassword(),
        ])
        ->assertRedirect(route('profile'))
        ->assertSessionHasErrors('current_password', null, 'passwordUpdate');
});

it('gagal mengubah password jika konfirmasi tidak sesuai', function () {
    $customer = customerUser(['password' => Hash::make('password')]);

    $this->actingAs($customer)
        ->from(route('profile'))
        ->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => validPassword(),
            'password_confirmation' => 'Beda123!',
        ])
        ->assertRedirect(route('profile'))
        ->assertSessionHasErrors('password', null, 'passwordUpdate');
});

it('gagal mengubah password jika form kosong', function () {
    $this->actingAs(customerUser())
        ->from(route('profile'))
        ->put(route('profile.password'), [])
        ->assertRedirect(route('profile'))
        ->assertSessionHasErrors(['current_password', 'password'], null, 'passwordUpdate');
});
