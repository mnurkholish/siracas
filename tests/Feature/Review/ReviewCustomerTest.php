<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer bisa memberi review pada produk dari transaksi selesai', function () {
    $customer = customerUser();
    $product = testProduct();
    [$transaction, $detail] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    $this->actingAs($customer)
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), [
            'rating' => 5,
            'isi' => 'Produk bagus.',
        ])
        ->assertRedirect(route('reviews.create'));

    $this->assertDatabaseHas('reviews', [
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'transaction_detail_id' => $detail->id,
        'rating' => 5,
    ]);
});

it('customer tidak bisa memberi review jika transaksi belum selesai atau bukan transaksinya', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    [$transaction, $detail] = testTransactionWithDetail($owner, testProduct(), ['status' => 'diterima']);

    $this->actingAs($owner)
        ->from(route('transactions.show', $transaction))
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), ['rating' => 5])
        ->assertRedirect(route('transactions.show', $transaction))
        ->assertSessionHasErrors('status');

    $transaction->update(['status' => 'selesai', 'completed_at' => now()]);

    $this->actingAs($otherCustomer)
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), ['rating' => 5])
        ->assertForbidden();
});

it('customer tidak bisa memberi review ganda pada detail transaksi yang sama', function () {
    $customer = customerUser();
    $product = testProduct();
    [$transaction, $detail] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);
    testReview($customer, $product, $detail);

    $this->actingAs($customer)
        ->from(route('reviews.create'))
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), ['rating' => 5])
        ->assertRedirect(route('reviews.create'))
        ->assertSessionHasErrors('review');
});

it('gagal membuat review jika rating kosong atau tidak valid', function () {
    $customer = customerUser();
    [$transaction, $detail] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    $this->actingAs($customer)
        ->from(route('reviews.show', $detail))
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), [])
        ->assertRedirect(route('reviews.show', $detail))
        ->assertSessionHasErrors('rating');

    $this->actingAs($customer)
        ->from(route('reviews.show', $detail))
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), ['rating' => 6])
        ->assertRedirect(route('reviews.show', $detail))
        ->assertSessionHasErrors('rating');
});

it('customer bisa melihat daftar dan detail review miliknya', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()]);
    $review = testReview($customer, $product, $detail);

    $this->actingAs($customer)
        ->get(route('reviews.index'))
        ->assertOk()
        ->assertViewHas('reviews');

    $this->actingAs($customer)
        ->get(route('products.reviews', $product))
        ->assertOk()
        ->assertViewHas('reviews');

    expect($review)->not->toBeNull();
});

it('customer bisa mengubah review jika masih dalam batas waktu', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()]);
    $review = testReview($customer, $product, $detail, ['rating' => 3]);

    $this->actingAs($customer)
        ->put(route('reviews.update', $review), [
            'rating' => 4,
            'isi' => 'Diperbarui.',
        ])
        ->assertRedirect(route('reviews.index'));

    expect($review->fresh()->rating)->toBe(4);
});

it('customer tidak bisa mengubah review jika melewati batas waktu dan gagal jika data tidak valid', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()->subDays(8)]);
    $review = testReview($customer, $product, $detail);
    $review->forceFill([
        'created_at' => now()->subDays(8),
        'updated_at' => now()->subDays(8),
    ])->save();

    $this->actingAs($customer)
        ->from(route('reviews.index'))
        ->put(route('reviews.update', $review), ['rating' => 4])
        ->assertRedirect(route('reviews.index'))
        ->assertSessionHasErrors('review');

    $newReview = testReview($customer, $product, $detail);

    $this->actingAs($customer)
        ->from(route('reviews.edit', $newReview))
        ->put(route('reviews.update', $newReview), ['rating' => 0])
        ->assertRedirect(route('reviews.edit', $newReview))
        ->assertSessionHasErrors('rating');
});
