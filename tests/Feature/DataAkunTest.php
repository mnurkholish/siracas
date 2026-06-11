<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin dapat melihat daftar customer dan menonaktifkan akun customer', function () {
    $admin = adminUser();
    $customer = customerUser();

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertViewHas('customers');

    $this->actingAs($admin)
        ->patch(route('admin.customers.status', $customer), [
            'status' => 'nonaktif',
        ])
        ->assertRedirect(route('admin.customers.index'));

    expect($customer->fresh()->is_active)->toBeFalse();
});

it('memvalidasi perubahan status akun customer', function () {
    $admin = adminUser();
    $customer = customerUser();

    $this->actingAs($admin)
        ->from(route('admin.customers.index'))
        ->patch(route('admin.customers.status', $customer), [
            'status' => 'pending',
        ])
        ->assertRedirect(route('admin.customers.index'))
        ->assertSessionHasErrors('status');
});

it('mencegah customer mengakses halaman data akun admin', function () {
    $this->actingAs(customerUser())
        ->get(route('admin.customers.index'))
        ->assertForbidden();
});

it('mengeluarkan customer nonaktif dari route customer', function () {
    $customer = customerUser(['is_active' => false]);

    $this->actingAs($customer)
        ->get(route('dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('tidak mengizinkan route status customer mengubah akun admin', function () {
    $admin = adminUser();
    $otherAdmin = adminUser();

    $this->actingAs($admin)
        ->patch(route('admin.customers.status', $otherAdmin), [
            'status' => 'nonaktif',
        ])
        ->assertNotFound();

    expect(User::find($otherAdmin->id)->is_active)->toBeTrue();
});
