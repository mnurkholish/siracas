<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin bisa membuat melihat detail mengubah menghapus dan memulihkan produk', function () {
    $admin = adminUser();

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            'nama_produk' => 'Cacing Lumbricus',
            'harga' => 35000,
            'stok' => 15,
            'satuan' => 'kg',
            'deskripsi' => 'Produk segar untuk pakan.',
        ])
        ->assertRedirect(route('admin.products.index'));

    $product = Product::where('nama_produk', 'Cacing Lumbricus')->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertViewHas('products');

    $this->actingAs($admin)
        ->get(route('admin.products.show', $product))
        ->assertOk()
        ->assertJsonPath('nama_produk', 'Cacing Lumbricus');

    $this->actingAs($admin)
        ->put(route('admin.products.update', $product), [
            'nama_produk' => 'Produk Baru',
            'harga' => 45000,
            'stok' => 8,
            'satuan' => 'paket',
            'deskripsi' => 'Deskripsi baru',
        ])
        ->assertRedirect(route('admin.products.index'));

    expect($product->fresh()->nama_produk)->toBe('Produk Baru');

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    expect(Product::withTrashed()->find($product->id)->trashed())->toBeTrue();

    $this->actingAs($admin)
        ->patch(route('admin.products.restore', $product->id))
        ->assertRedirect(route('admin.products.archives'));

    expect(Product::find($product->id))->not->toBeNull();
});

it('customer bisa melihat daftar dan detail produk', function () {
    $customer = customerUser();
    $product = testProduct();

    $this->actingAs($customer)
        ->get(route('products.index'))
        ->assertOk()
        ->assertViewHas('products');

    $this->actingAs($customer)
        ->get(route('products.show', $product))
        ->assertOk()
        ->assertViewHas('product');
});

it('gagal tambah atau ubah produk jika data kosong atau tidak valid', function () {
    $admin = adminUser();
    $product = testProduct();

    $this->actingAs($admin)
        ->from(route('admin.products.index'))
        ->post(route('admin.products.store'), [])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHasErrors(['nama_produk', 'harga', 'stok', 'satuan']);

    $this->actingAs($admin)
        ->from(route('admin.products.index'))
        ->put(route('admin.products.update', $product), [
            'nama_produk' => '',
            'harga' => -1,
            'stok' => -2,
            'satuan' => 'ton',
        ])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHasErrors(['nama_produk', 'harga', 'stok', 'satuan']);
});

it('customer tidak boleh membuat mengubah atau menghapus produk', function () {
    $customer = customerUser();
    $product = testProduct();

    $this->actingAs($customer)
        ->post(route('admin.products.store'), [
            'nama_produk' => 'Cacing',
            'harga' => 10000,
            'stok' => 1,
            'satuan' => 'kg',
        ])
        ->assertForbidden();

    $this->actingAs($customer)
        ->put(route('admin.products.update', $product), [
            'nama_produk' => 'Cacing Baru',
            'harga' => 10000,
            'stok' => 1,
            'satuan' => 'kg',
        ])
        ->assertForbidden();

    $this->actingAs($customer)
        ->delete(route('admin.products.destroy', $product))
        ->assertForbidden();
});
