<?php

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('memproses checkout item keranjang menjadi transaksi dan mengurangi stok', function () {
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
        ->and((int) $product->fresh()->stok)->toBe(7);

    $this->assertDatabaseHas('transaction_details', [
        'transaction_id' => $transaction->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'harga_saat_transaksi' => 30000,
    ]);

    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
});

it('menolak checkout menggunakan alamat customer lain', function () {
    $customer = customerUser();
    $otherCustomer = customerUser();
    $otherAddress = testAddressFor($otherCustomer);
    testCartItem($customer, testProduct(), 1);

    $this->actingAs($customer)
        ->from(route('checkout.index'))
        ->post(route('checkout.store'), [
            'address_id' => $otherAddress->id,
        ])
        ->assertRedirect(route('checkout.index'))
        ->assertSessionHasErrors('address_id');
});

it('customer dapat membatalkan transaksi menunggu pembayaran dan mengembalikan stok produk', function () {
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

it('hanya menyelesaikan transaksi yang sudah diterima tanpa garansi tertunda', function () {
    $customer = customerUser();
    $product = testProduct();
    [$transaction] = testTransactionWithDetail($customer, $product, [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'tidak_ada',
    ]);

    $this->actingAs($customer)
        ->patch(route('transactions.complete', $transaction))
        ->assertRedirect(route('transactions.show', $transaction));

    $transaction->refresh();

    expect($transaction->status)->toBe('selesai')
        ->and($transaction->completed_at)->not->toBeNull();
});

it('menolak penyelesaian sebelum transaksi diterima', function () {
    $customer = customerUser();
    $product = testProduct();
    [$transaction] = testTransactionWithDetail($customer, $product, [
        'status' => 'diproses',
    ]);

    $this->actingAs($customer)
        ->from(route('transactions.show', $transaction))
        ->patch(route('transactions.complete', $transaction))
        ->assertRedirect(route('transactions.show', $transaction))
        ->assertSessionHasErrors('status');
});

it('mencegah customer melihat transaksi milik customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    [$transaction] = testTransactionWithDetail($owner, testProduct());

    $this->actingAs($otherCustomer)
        ->get(route('transactions.show', $transaction))
        ->assertForbidden();
});

it('admin dapat memindahkan transaksi dibayar sesuai alur status yang tersedia', function () {
    $admin = adminUser();
    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'dibayar',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.transactions.status', $transaction), [
            'status' => 'diproses',
        ])
        ->assertRedirect(route('admin.transactions.show', $transaction));

    expect($transaction->fresh()->status)->toBe('diproses');
});

it('menolak perubahan status transaksi admin yang tidak valid', function () {
    $admin = adminUser();
    $customer = customerUser();
    [$transaction] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'menunggu_pembayaran',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.transactions.show', $transaction))
        ->patch(route('admin.transactions.status', $transaction), [
            'status' => 'diproses',
        ])
        ->assertRedirect(route('admin.transactions.show', $transaction))
        ->assertSessionHasErrors('status');

    expect($transaction->fresh()->status)->toBe('menunggu_pembayaran');
});
