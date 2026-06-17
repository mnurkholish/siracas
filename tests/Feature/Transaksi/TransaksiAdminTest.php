<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin bisa melihat daftar dan detail transaksi', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), ['status' => 'dibayar']);

    $this->actingAs($admin)
        ->get(route('admin.transactions.index'))
        ->assertOk()
        ->assertViewHas('transactions');

    $this->actingAs($admin)
        ->get(route('admin.transactions.show', $transaction))
        ->assertOk()
        ->assertViewHas('transaction');
});

it('admin bisa mengubah status transaksi sesuai urutan yang diizinkan', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), ['status' => 'dibayar']);

    $this->actingAs($admin)
        ->patch(route('admin.transactions.status', $transaction), ['status' => 'diproses'])
        ->assertRedirect(route('admin.transactions.show', $transaction));

    expect($transaction->fresh()->status)->toBe('diproses');
});

it('admin bisa mengubah ongkir transaksi saat menunggu pembayaran', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), [
        'status' => 'menunggu_pembayaran',
        'ongkir' => 0,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.transactions.shipping', $transaction), ['ongkir' => 12000])
        ->assertRedirect(route('admin.transactions.show', $transaction));

    expect((float) $transaction->fresh()->ongkir)->toBe(12000.0);
});

it('gagal ubah transaksi jika data tidak valid', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), ['status' => 'menunggu_pembayaran']);

    $this->actingAs($admin)
        ->from(route('admin.transactions.show', $transaction))
        ->patch(route('admin.transactions.status', $transaction), ['status' => 'diproses'])
        ->assertRedirect(route('admin.transactions.show', $transaction))
        ->assertSessionHasErrors('status');

    $this->actingAs($admin)
        ->from(route('admin.transactions.show', $transaction))
        ->patch(route('admin.transactions.shipping', $transaction), ['ongkir' => -1])
        ->assertRedirect(route('admin.transactions.show', $transaction))
        ->assertSessionHasErrors('ongkir');
});

it('customer tidak boleh mengakses halaman transaksi admin', function () {
    $transaction = testTransactionWithDetail(customerUser(), testProduct())[0];

    $this->actingAs(customerUser())
        ->get(route('admin.transactions.show', $transaction))
        ->assertForbidden();
});
