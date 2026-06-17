<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin dan customer bisa melihat data akunnya', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.profile'))
        ->assertOk();

    $this->actingAs(customerUser())
        ->get(route('profile'))
        ->assertOk();
});

it('admin bisa melihat daftar dan detail akun customer', function () {
    $admin = adminUser();
    $customer = customerUser();

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertViewHas('customers');

    $this->actingAs($admin)
        ->get(route('admin.customers.show', $customer->id))
        ->assertOk()
        ->assertJsonPath('id', $customer->id)
        ->assertJsonPath('email', $customer->email);
});

it('customer tidak boleh mengakses halaman akun admin atau customer lain', function () {
    $this->actingAs(customerUser())
        ->get(route('admin.customers.index'))
        ->assertForbidden();
});

it('customer dan admin bisa mengubah profil', function () {
    $customer = customerUser();
    $admin = adminUser();

    $this->actingAs($customer)
        ->put(route('profile.update'), [
            'username' => 'customerupdate',
            'email' => 'customerupdate@example.test',
            'nomor_hp' => '081234567893',
            'tanggal_lahir' => now()->subYears(21)->toDateString(),
            'jenis_kelamin' => 'perempuan',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($customer->fresh()->username)->toBe('customerupdate');

    $this->actingAs($admin)
        ->put(route('admin.profile.update'), [
            'username' => 'adminupdate',
            'email' => 'adminupdate@example.test',
            'nomor_hp' => '081234567894',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'laki-laki',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($admin->fresh()->username)->toBe('adminupdate');
});

it('gagal mengubah profil jika data kosong atau tidak valid', function () {
    $customer = customerUser();

    $this->actingAs($customer)
        ->from(route('profile'))
        ->put(route('profile.update'), [])
        ->assertRedirect(route('profile'))
        ->assertSessionHasErrors(['username', 'email', 'tanggal_lahir', 'jenis_kelamin', 'nomor_hp'], null, 'profileUpdate');

    $this->actingAs($customer)
        ->from(route('profile'))
        ->put(route('profile.update'), [
            'username' => 'customer',
            'email' => 'salah',
            'nomor_hp' => '628123',
            'tanggal_lahir' => now()->subYears(10)->toDateString(),
            'jenis_kelamin' => 'lainnya',
        ])
        ->assertRedirect(route('profile'))
        ->assertSessionHasErrors(['email', 'nomor_hp', 'tanggal_lahir', 'jenis_kelamin'], null, 'profileUpdate');
});

it('batal ubah profil tidak mengubah data', function () {
    $customer = customerUser(['username' => 'namalama']);

    $this->actingAs($customer)
        ->get(route('profile'))
        ->assertOk();

    expect($customer->fresh()->username)->toBe('namalama');
});

it('admin bisa menonaktifkan customer dan tidak bisa mengubah status akun admin lain', function () {
    $admin = adminUser();
    $customer = customerUser();
    $otherAdmin = adminUser();

    $this->actingAs($admin)
        ->patch(route('admin.customers.status', $customer), ['status' => 'nonaktif'])
        ->assertRedirect(route('admin.customers.index'));

    expect($customer->fresh()->is_active)->toBeFalse();

    $this->actingAs($admin)
        ->patch(route('admin.customers.status', $otherAdmin), ['status' => 'nonaktif'])
        ->assertNotFound();

    expect($otherAdmin->fresh()->is_active)->toBeTrue();
});
