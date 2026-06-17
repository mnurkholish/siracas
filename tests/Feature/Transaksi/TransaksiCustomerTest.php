<?php

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer bisa membuat transaksi dari keranjang dan stok produk berkurang', function () {
    adminUser();
    $customer = customerUser();
    $address = testAddressFor($customer);
    $product = testProduct(['stok' => 10, 'harga' => 30000]);
    $cartItem = testCartItem($customer, $product, 3);

    $this->actingAs($customer)
        ->post(route('checkout.store'), [
            'address_id' => $address->id,
            'catatan' => 'Kirim sore.',
        ])
        ->assertRedirect();

    $transaction = Transaction::where('user_id', $customer->id)->first();

    expect($transaction)->not->toBeNull()
        ->and($transaction->status)->toBe('menunggu_pembayaran')
        ->and($transaction->catatan)->toBe('Kirim sore.')
        ->and((float) $transaction->ongkir)->toBe(0.0)
        ->and((int) $product->fresh()->stok)->toBe(7);

    $this->assertDatabaseHas('transaction_details', [
        'transaction_id' => $transaction->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'harga_saat_transaksi' => 30000,
    ]);

    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
});

it('customer bisa membuat transaksi langsung dari halaman produk', function () {
    adminUser();
    $customer = customerUser();
    $address = testAddressFor($customer);
    $product = testProduct(['stok' => 5, 'harga' => 20000]);

    $this->actingAs($customer)
        ->post(route('products.buy-now.store', $product), [
            'address_id' => $address->id,
            'quantity' => 2,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('transaction_details', [
        'product_id' => $product->id,
        'quantity' => 2,
        'harga_saat_transaksi' => 20000,
    ]);

    expect((int) $product->fresh()->stok)->toBe(3);
});

it('gagal membuat transaksi jika data kosong atau alamat tidak valid', function () {
    $customer = customerUser();
    $otherCustomer = customerUser();
    $otherAddress = testAddressFor($otherCustomer);
    testCartItem($customer, testProduct(), 1);

    $this->actingAs($customer)
        ->from(route('checkout.index'))
        ->post(route('checkout.store'), [])
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHasErrors('address_id');

    $this->actingAs($customer)
        ->from(route('checkout.index'))
        ->post(route('checkout.store'), ['address_id' => $otherAddress->id])
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHasErrors('address_id');
});

it('customer bisa melihat daftar dan detail transaksinya', function () {
    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), ['status' => 'diproses']);

    $this->actingAs($customer)
        ->get(route('transactions.index'))
        ->assertOk()
        ->assertViewHas('transactions');

    $this->actingAs($customer)
        ->get(route('transactions.show', $transaction))
        ->assertOk()
        ->assertViewHas('transaction');
});

it('customer tidak bisa melihat transaksi milik customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    [$transaction] = testTransactionWithDetail($owner, testProduct());

    $this->actingAs($otherCustomer)
        ->get(route('transactions.show', $transaction))
        ->assertForbidden();
});

it('customer bisa membatalkan transaksi menunggu pembayaran dan stok dikembalikan', function () {
    $customer = customerUser();
    $product = testProduct(['stok' => 4]);
    [$transaction] = testTransactionWithDetail($customer, $product, [
        'status' => 'menunggu_pembayaran',
    ], [
        'quantity' => 2,
    ]);

    $this->actingAs($customer)
        ->patch(route('transactions.cancel', $transaction))
        ->assertRedirect(route('transactions.show', $transaction));

    expect($transaction->fresh()->status)->toBe('dibatalkan')
        ->and((int) $product->fresh()->stok)->toBe(6);
});

it('customer tidak bisa membatalkan transaksi jika status tidak memperbolehkan', function () {
    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), ['status' => 'dibayar']);

    $this->actingAs($customer)
        ->from(route('transactions.show', $transaction))
        ->patch(route('transactions.cancel', $transaction))
        ->assertRedirect(route('transactions.show', $transaction))
        ->assertSessionHasErrors('status');
});
