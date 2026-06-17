<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer bisa mengajukan garansi jika transaksi diterima dan masih dalam batas waktu', function () {
    adminUser(['nomor_hp' => '081111111111']);
    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'tidak_ada',
    ]);

    $this->actingAs($customer)
        ->patch(route('transactions.warranty', $transaction))
        ->assertRedirect(route('transactions.show', $transaction));

    $transaction->refresh();

    expect($transaction->warranty_status)->toBe('diajukan')
        ->and($transaction->warranty_claimed_at)->not->toBeNull();
});

it('customer tidak bisa mengajukan garansi jika status belum diterima atau melewati batas waktu', function () {
    $customer = customerUser();
    [$notReceived] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'dikirim',
        'warranty_status' => 'tidak_ada',
    ]);
    [$expired] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'diterima',
        'received_at' => now()->subDays(2),
        'warranty_status' => 'tidak_ada',
    ]);

    $this->actingAs($customer)
        ->from(route('transactions.show', $notReceived))
        ->patch(route('transactions.warranty', $notReceived))
        ->assertRedirect(route('transactions.show', $notReceived))
        ->assertSessionHasErrors('warranty');

    $this->actingAs($customer)
        ->from(route('transactions.show', $expired))
        ->patch(route('transactions.warranty', $expired))
        ->assertRedirect(route('transactions.show', $expired))
        ->assertSessionHasErrors('warranty');
});

it('customer tidak bisa mengajukan garansi untuk transaksi milik customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    [$transaction] = testTransactionWithDetail($owner, testProduct(), [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'tidak_ada',
    ]);

    $this->actingAs($otherCustomer)
        ->patch(route('transactions.warranty', $transaction))
        ->assertForbidden();
});

it('link WhatsApp garansi tersedia pada detail transaksi diterima jika admin memiliki nomor HP', function () {
    adminUser(['nomor_hp' => '081111111111']);
    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'diterima',
        'received_at' => now(),
    ]);

    $this->actingAs($customer)
        ->get(route('transactions.show', $transaction))
        ->assertOk()
        ->assertViewHas('warrantyWhatsappUrl', fn (?string $url) => str_starts_with((string) $url, 'https://wa.me/628'));
});
