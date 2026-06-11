<?php

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('customer dapat memberi ulasan pada detail transaksi selesai dalam masa penilaian', function () {
    $customer = customerUser();
    $product = testProduct();
    [$transaction, $detail] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    $this->actingAs($customer)
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), [
            'rating' => 5,
            'isi' => 'Produk bagus dan sesuai.',
        ])
        ->assertRedirect(route('reviews.create'));

    $this->assertDatabaseHas('reviews', [
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'transaction_detail_id' => $detail->id,
        'rating' => 5,
        'isi' => 'Produk bagus dan sesuai.',
    ]);
});

it('menolak ulasan sebelum transaksi selesai', function () {
    $customer = customerUser();
    [$transaction, $detail] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'diterima',
        'completed_at' => null,
    ]);

    $this->actingAs($customer)
        ->from(route('transactions.show', $transaction))
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), [
            'rating' => 5,
            'isi' => 'Belum selesai.',
        ])
        ->assertRedirect(route('transactions.show', $transaction))
        ->assertSessionHasErrors('status');
});

it('menolak ulasan ganda untuk detail transaksi yang sama', function () {
    $customer = customerUser();
    $product = testProduct();
    [$transaction, $detail] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    Review::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'transaction_detail_id' => $detail->id,
        'rating' => 4,
        'isi' => 'Sudah pernah review.',
    ]);

    $this->actingAs($customer)
        ->from(route('reviews.create'))
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), [
            'rating' => 5,
            'isi' => 'Review kedua.',
        ])
        ->assertRedirect(route('reviews.create'))
        ->assertSessionHasErrors('review');
});

it('mencegah customer mengulas transaksi milik customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    [$transaction, $detail] = testTransactionWithDetail($owner, testProduct(), [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    $this->actingAs($otherCustomer)
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), [
            'rating' => 5,
            'isi' => 'Tidak boleh.',
        ])
        ->assertForbidden();
});

it('memvalidasi batas nilai rating ulasan', function () {
    $customer = customerUser();
    [$transaction, $detail] = testTransactionWithDetail($customer, testProduct(), [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    $this->actingAs($customer)
        ->from(route('reviews.show', $detail))
        ->post(route('transactions.details.reviews.store', [$transaction, $detail]), [
            'rating' => 6,
            'isi' => 'Rating tidak valid.',
        ])
        ->assertRedirect(route('reviews.show', $detail))
        ->assertSessionHasErrors('rating');
});

it('customer dapat memperbarui ulasan yang masih baru', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);
    $review = Review::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'transaction_detail_id' => $detail->id,
        'rating' => 3,
        'isi' => 'Awal.',
    ]);

    $this->actingAs($customer)
        ->put(route('reviews.update', $review), [
            'rating' => 4,
            'isi' => 'Diperbarui.',
        ])
        ->assertRedirect(route('reviews.index'));

    $this->assertDatabaseHas('reviews', [
        'id' => $review->id,
        'rating' => 4,
        'isi' => 'Diperbarui.',
    ]);
});

it('mencegah pengeditan ulasan yang lebih lama dari tujuh hari', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'completed_at' => now()->subDays(8),
    ]);
    $review = Review::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'transaction_detail_id' => $detail->id,
        'rating' => 3,
        'isi' => 'Lama.',
    ]);
    $review->forceFill([
        'created_at' => now()->subDays(8),
        'updated_at' => now()->subDays(8),
    ])->save();

    $this->actingAs($customer)
        ->from(route('reviews.index'))
        ->put(route('reviews.update', $review), [
            'rating' => 4,
            'isi' => 'Terlambat.',
        ])
        ->assertRedirect(route('reviews.index'))
        ->assertSessionHasErrors('review');
});

it('admin dapat membalas ulasan customer', function () {
    $admin = adminUser();
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);
    $review = Review::create([
        'user_id' => $customer->id,
        'product_id' => $product->id,
        'transaction_detail_id' => $detail->id,
        'rating' => 5,
        'isi' => 'Mantap.',
    ]);

    $this->actingAs($admin)
        ->put(route('admin.reviews.reply', $review), [
            'admin_reply' => 'Terima kasih.',
        ])
        ->assertRedirect(route('admin.reviews.index'));

    $review->refresh();

    expect($review->admin_reply)->toBe('Terima kasih.')
        ->and($review->admin_replied_by)->toBe($admin->id)
        ->and($review->admin_replied_at)->not->toBeNull();
});
