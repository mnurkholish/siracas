<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('menampilkan hanya transaksi aktif customer pada halaman transaksi aktif', function () {
    $customer = customerUser();
    $activeProduct = testProduct(['nama_produk' => 'Produk Aktif']);
    $historyProduct = testProduct(['nama_produk' => 'Produk Riwayat']);
    [$activeTransaction] = testTransactionWithDetail($customer, $activeProduct, ['status' => 'diproses']);
    [$historyTransaction] = testTransactionWithDetail($customer, $historyProduct, ['status' => 'selesai', 'completed_at' => now()]);

    $this->actingAs($customer)
        ->get(route('transactions.index'))
        ->assertOk()
        ->assertViewHas('transactions', function ($transactions) use ($activeTransaction, $historyTransaction) {
            return $transactions->contains('id', $activeTransaction->id)
                && ! $transactions->contains('id', $historyTransaction->id);
        });
});

it('menampilkan hanya transaksi selesai dibatalkan atau kedaluwarsa pada riwayat customer', function () {
    $customer = customerUser();
    [$activeTransaction] = testTransactionWithDetail($customer, testProduct(), ['status' => 'dikirim']);
    [$historyTransaction] = testTransactionWithDetail($customer, testProduct(), ['status' => 'dibatalkan']);

    $this->actingAs($customer)
        ->get(route('transactions.history'))
        ->assertOk()
        ->assertViewHas('transactions', function ($transactions) use ($activeTransaction, $historyTransaction) {
            return $transactions->contains('id', $historyTransaction->id)
                && ! $transactions->contains('id', $activeTransaction->id);
        });
});

it('mencegah customer melihat transaksi customer lain pada halaman riwayat', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    [$ownerTransaction] = testTransactionWithDetail($owner, testProduct(), ['status' => 'selesai', 'completed_at' => now()]);
    [$otherTransaction] = testTransactionWithDetail($otherCustomer, testProduct(), ['status' => 'selesai', 'completed_at' => now()]);

    $this->actingAs($otherCustomer)
        ->get(route('transactions.history'))
        ->assertOk()
        ->assertViewHas('transactions', function ($transactions) use ($ownerTransaction, $otherTransaction) {
            return $transactions->contains('id', $otherTransaction->id)
                && ! $transactions->contains('id', $ownerTransaction->id);
        });
});
