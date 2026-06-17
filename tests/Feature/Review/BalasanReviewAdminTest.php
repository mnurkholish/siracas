<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin bisa melihat daftar review customer', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()]);
    testReview($customer, $product, $detail);

    $this->actingAs(adminUser())
        ->get(route('admin.reviews.index'))
        ->assertOk()
        ->assertViewHas('reviews');
});

it('admin bisa membalas dan mengubah balasan review', function () {
    $admin = adminUser();
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()]);
    $review = testReview($customer, $product, $detail);

    $this->actingAs($admin)
        ->put(route('admin.reviews.reply', $review), ['admin_reply' => 'Terima kasih.'])
        ->assertRedirect(route('admin.reviews.index'));

    expect($review->fresh()->admin_reply)->toBe('Terima kasih.')
        ->and($review->fresh()->admin_replied_by)->toBe($admin->id);

    $this->actingAs($admin)
        ->put(route('admin.reviews.reply', $review), ['admin_reply' => 'Sudah kami cek.'])
        ->assertRedirect(route('admin.reviews.index'));

    expect($review->fresh()->admin_reply)->toBe('Sudah kami cek.');
});

it('admin bisa menghapus atau mengosongkan balasan review', function () {
    $admin = adminUser();
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()]);
    $review = testReview($customer, $product, $detail, [
        'admin_reply' => 'Balasan lama',
        'admin_replied_by' => $admin->id,
        'admin_replied_at' => now(),
    ]);

    $this->actingAs($admin)
        ->put(route('admin.reviews.reply', $review), ['admin_reply' => ''])
        ->assertRedirect(route('admin.reviews.index'));

    $review->refresh();

    expect($review->admin_reply)->toBeNull()
        ->and($review->admin_replied_by)->toBeNull()
        ->and($review->admin_replied_at)->toBeNull();
});

it('gagal membalas review jika balasan melebihi batas validasi', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()]);
    $review = testReview($customer, $product, $detail);

    $this->actingAs(adminUser())
        ->from(route('admin.reviews.create', $review))
        ->put(route('admin.reviews.reply', $review), ['admin_reply' => str_repeat('a', 2001)])
        ->assertRedirect(route('admin.reviews.create', $review))
        ->assertSessionHasErrors('admin_reply');
});

it('customer tidak boleh mengakses fitur balasan admin', function () {
    $customer = customerUser();
    $product = testProduct();
    [, $detail] = testTransactionWithDetail($customer, $product, ['status' => 'selesai', 'completed_at' => now()]);
    $review = testReview($customer, $product, $detail);

    $this->actingAs($customer)
        ->put(route('admin.reviews.reply', $review), ['admin_reply' => 'Tidak boleh'])
        ->assertForbidden();
});
