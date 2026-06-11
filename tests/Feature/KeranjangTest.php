<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer dapat menambahkan produk ke keranjang dan menggabungkan quantity', function () {
    $customer = customerUser();
    $product = testProduct(['stok' => 5, 'harga' => 12000]);

    $this->actingAs($customer)
        ->post(route('cart.store', $product), ['quantity' => 2])
        ->assertRedirect(route('cart.index'));

    $this->actingAs($customer)
        ->post(route('cart.store', $product), ['quantity' => 3])
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('carts', ['user_id' => $customer->id]);
    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 5,
        'harga_saat_dimasukkan' => 12000,
    ]);
});

it('menolak quantity keranjang yang melebihi stok produk', function () {
    $customer = customerUser();
    $product = testProduct(['stok' => 2]);

    $this->actingAs($customer)
        ->from(route('products.show', $product))
        ->post(route('cart.store', $product), ['quantity' => 3])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHasErrors('quantity');
});

it('customer dapat memperbarui quantity item keranjang dan menghapus dengan quantity nol', function () {
    $customer = customerUser();
    $product = testProduct(['stok' => 5]);
    $cartItem = testCartItem($customer, $product, 2);

    $this->actingAs($customer)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 4])
        ->assertRedirect(route('cart.index'));

    expect($cartItem->fresh()->quantity)->toBe(4);

    $this->actingAs($customer)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 0])
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
});

it('mencegah customer mengubah item keranjang milik customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    $product = testProduct();
    $cartItem = testCartItem($owner, $product, 1);

    $this->actingAs($otherCustomer)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 2])
        ->assertForbidden();
});
