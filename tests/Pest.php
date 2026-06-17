<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function adminUser(array $attributes = []): App\Models\User
{
    return App\Models\User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        ...$attributes,
    ]);
}

function customerUser(array $attributes = []): App\Models\User
{
    return App\Models\User::factory()->create([
        'role' => 'customer',
        'is_active' => true,
        ...$attributes,
    ]);
}

function testAddressFor(App\Models\User $user, array $attributes = []): App\Models\Address
{
    $provinsi = App\Models\Provinsi::create([
        'code' => 'P'.fake()->unique()->numerify('####'),
        'nama' => 'Jawa Barat',
    ]);

    $kota = App\Models\Kota::create([
        'provinsi_id' => $provinsi->id,
        'code' => 'K'.fake()->unique()->numerify('####'),
        'nama' => 'Bogor',
    ]);

    $kecamatan = App\Models\Kecamatan::create([
        'kota_id' => $kota->id,
        'code' => 'C'.fake()->unique()->numerify('####'),
        'nama' => 'Cibinong',
    ]);

    return App\Models\Address::create([
        'user_id' => $user->id,
        'kecamatan_id' => $kecamatan->id,
        'detail_alamat' => 'Jl. Test No. 1',
        ...$attributes,
    ]);
}

function testProduct(array $attributes = []): App\Models\Product
{
    return App\Models\Product::factory()->create([
        'nama_produk' => 'Kascing Test',
        'harga' => 25000,
        'stok' => 20,
        'satuan' => 'kg',
        ...$attributes,
    ]);
}

function testCartItem(App\Models\User $user, App\Models\Product $product, int $quantity = 1): App\Models\CartItem
{
    $cart = $user->cart()->firstOrCreate([]);

    return $cart->cartItems()->create([
        'product_id' => $product->id,
        'quantity' => $quantity,
        'harga_saat_dimasukkan' => $product->harga,
    ]);
}

function testTransactionWithDetail(
    App\Models\User $user,
    App\Models\Product $product,
    array $transactionAttributes = [],
    array $detailAttributes = [],
): array {
    $address = $transactionAttributes['address'] ?? testAddressFor($user);
    unset($transactionAttributes['address']);

    $transaction = App\Models\Transaction::create([
        'user_id' => $user->id,
        'address_id' => $address->id,
        'tanggal' => now(),
        'status' => 'menunggu_pembayaran',
        'ongkir' => 0,
        ...$transactionAttributes,
    ]);

    $detail = $transaction->transactionDetails()->create([
        'product_id' => $product->id,
        'quantity' => 2,
        'harga_saat_transaksi' => $product->harga,
        ...$detailAttributes,
    ]);

    return [$transaction, $detail];
}
