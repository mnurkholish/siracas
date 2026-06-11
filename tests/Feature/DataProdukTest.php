<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin dapat membuat produk', function () {
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

    $this->assertDatabaseHas('products', [
        'nama_produk' => 'Cacing Lumbricus',
        'harga' => 35000,
        'stok' => 15,
        'satuan' => 'kg',
    ]);
});

it('memvalidasi field wajib produk dan satuan yang diizinkan', function () {
    $admin = adminUser();

    $this->actingAs($admin)
        ->from(route('admin.products.index'))
        ->post(route('admin.products.store'), [
            'nama_produk' => '',
            'harga' => -1,
            'stok' => -2,
            'satuan' => 'ton',
        ])
        ->assertRedirect(route('admin.products.index'))
        ->assertSessionHasErrors(['nama_produk', 'harga', 'stok', 'satuan']);
});

it('admin dapat memperbarui mengarsipkan dan memulihkan produk', function () {
    $admin = adminUser();
    $product = testProduct(['nama_produk' => 'Produk Lama']);

    $this->actingAs($admin)
        ->put(route('admin.products.update', $product), [
            'nama_produk' => 'Produk Baru',
            'harga' => 45000,
            'stok' => 8,
            'satuan' => 'paket',
            'deskripsi' => 'Deskripsi baru',
        ])
        ->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'nama_produk' => 'Produk Baru',
        'satuan' => 'paket',
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    expect(Product::withTrashed()->find($product->id)->trashed())->toBeTrue();

    $this->actingAs($admin)
        ->patch(route('admin.products.restore', $product->id))
        ->assertRedirect(route('admin.products.archives'));

    expect(Product::find($product->id))->not->toBeNull();
});

it('mencegah customer mengelola produk admin', function () {
    $this->actingAs(customerUser())
        ->post(route('admin.products.store'), [
            'nama_produk' => 'Cacing',
            'harga' => 10000,
            'stok' => 1,
            'satuan' => 'kg',
        ])
        ->assertForbidden();
});
