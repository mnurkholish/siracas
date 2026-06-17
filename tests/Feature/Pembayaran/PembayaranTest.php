<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('membuat pembayaran Midtrans dengan gross amount produk ditambah ongkir', function () {
    config()->set('services.midtrans.server_key', 'server-key-test');
    config()->set('services.midtrans.snap_url', 'https://midtrans.test/snap');

    Http::fake([
        'https://midtrans.test/snap' => Http::response(['token' => 'snap-token-test']),
    ]);

    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(['harga' => 30000]), [
        'status' => 'menunggu_pembayaran',
        'ongkir' => 10000,
    ], [
        'quantity' => 2,
        'harga_saat_transaksi' => 30000,
    ]);

    $this->actingAs($customer)
        ->postJson(route('transactions.pay', $transaction))
        ->assertOk()
        ->assertJsonPath('snap_token', 'snap-token-test');

    Http::assertSent(fn ($request) => $request['transaction_details']['gross_amount'] === 70000);

    expect($transaction->fresh()->snap_token)->toBe('snap-token-test');
});

it('gagal membuat pembayaran jika ongkir belum ditentukan atau konfigurasi Midtrans kosong', function () {
    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'menunggu_pembayaran',
        'ongkir' => 0,
    ]);

    $this->actingAs($customer)
        ->from(route('transactions.show', $transaction))
        ->post(route('transactions.pay', $transaction))
        ->assertRedirect(route('transactions.show', $transaction))
        ->assertSessionHasErrors('ongkir');

    $transaction->update(['ongkir' => 10000]);
    config()->set('services.midtrans.server_key', null);

    $this->actingAs($customer)
        ->from(route('transactions.show', $transaction))
        ->post(route('transactions.pay', $transaction))
        ->assertRedirect(route('transactions.show', $transaction))
        ->assertSessionHasErrors('payment');
});

it('callback Midtrans mengubah status pembayaran sesuai handler', function () {
    config()->set('services.midtrans.server_key', 'server-key-test');

    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'menunggu_pembayaran',
        'order_id' => 'SIRACAS-TEST-1',
        'ongkir' => 10000,
    ]);

    $grossAmount = '60000.00';
    $signature = hash('sha512', $transaction->order_id.'200'.$grossAmount.'server-key-test');

    $this->postJson(route('midtrans.callback'), [
        'order_id' => $transaction->order_id,
        'status_code' => '200',
        'gross_amount' => $grossAmount,
        'signature_key' => $signature,
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
    ])->assertOk()
        ->assertJsonPath('message', 'Callback processed.');

    $transaction->refresh();

    expect($transaction->status)->toBe('dibayar')
        ->and($transaction->payment_type)->toBe('bank_transfer')
        ->and($transaction->paid_at)->not->toBeNull();
});

it('callback Midtrans menolak signature tidak valid', function () {
    config()->set('services.midtrans.server_key', 'server-key-test');

    $this->postJson(route('midtrans.callback'), [
        'order_id' => 'SIRACAS-TEST-404',
        'status_code' => '200',
        'gross_amount' => '10000.00',
        'signature_key' => 'invalid',
        'transaction_status' => 'settlement',
    ])->assertForbidden();
});
