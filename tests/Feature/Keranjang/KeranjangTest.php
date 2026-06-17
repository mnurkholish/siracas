<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer bisa melihat keranjang dan menambahkan produk ke keranjang', function () {
    $customer = customerUser();
    $product = testProduct(['stok' => 5, 'harga' => 12000]);

    $this->actingAs($customer)
        ->get(route('cart.index'))
        ->assertOk()
        ->assertViewHas('cartItems');

    $this->actingAs($customer)
        ->post(route('cart.store', $product), ['quantity' => 2])
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 2,
        'harga_saat_dimasukkan' => 12000,
    ]);
});

it('customer bisa mengubah jumlah dan menghapus produk dari keranjang', function () {
    $customer = customerUser();
    $product = testProduct(['stok' => 5]);
    $cartItem = testCartItem($customer, $product, 2);

    $this->actingAs($customer)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 4])
        ->assertRedirect(route('cart.index'));

    expect($cartItem->fresh()->quantity)->toBe(4);

    $this->actingAs($customer)
        ->delete(route('cart.items.destroy', $cartItem))
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
});

it('gagal menambahkan produk jika jumlah melebihi stok atau tidak valid', function () {
    $customer = customerUser();
    $product = testProduct(['stok' => 2]);

    $this->actingAs($customer)
        ->from(route('products.show', $product))
        ->post(route('cart.store', $product), ['quantity' => 3])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHasErrors('quantity');

    $this->actingAs($customer)
        ->from(route('products.show', $product))
        ->post(route('cart.store', $product), ['quantity' => 0])
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHasErrors('quantity');
});

it('admin tidak boleh menggunakan fitur keranjang customer', function () {
    $product = testProduct();

    $this->actingAs(adminUser())
        ->post(route('cart.store', $product), ['quantity' => 1])
        ->assertForbidden();
});

it('customer tidak boleh mengubah keranjang milik customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    $cartItem = testCartItem($owner, testProduct(), 1);

    $this->actingAs($otherCustomer)
        ->patch(route('cart.items.update', $cartItem), ['quantity' => 2])
        ->assertForbidden();
});
