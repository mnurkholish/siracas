<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin bisa melihat transaksi dengan pengajuan garansi', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'diajukan',
        'warranty_claimed_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.transactions.show', $transaction))
        ->assertOk()
        ->assertViewHas('transaction');
});

it('admin bisa menerima garansi mengisi nominal dan catatan refund', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(['harga' => 20000]), [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'diajukan',
        'ongkir' => 10000,
    ], [
        'quantity' => 2,
        'harga_saat_transaksi' => 20000,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.transactions.warranty', $transaction), [
            'decision' => 'diterima',
            'refund_amount' => 15000,
            'refund_note' => 'Sebagian produk rusak.',
        ])
        ->assertRedirect(route('admin.transactions.show', $transaction));

    $transaction->refresh();

    expect($transaction->status)->toBe('selesai')
        ->and($transaction->warranty_status)->toBe('diterima')
        ->and((float) $transaction->refund_amount)->toBe(15000.0)
        ->and($transaction->refunded_at)->not->toBeNull()
        ->and($transaction->pendapatanBersih())->toBe(35000.0);
});

it('admin bisa menolak garansi dengan catatan wajib', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'diajukan',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.transactions.warranty', $transaction), [
            'decision' => 'ditolak',
            'refund_amount' => 0,
            'refund_note' => 'Bukti tidak sesuai.',
        ])
        ->assertRedirect(route('admin.transactions.show', $transaction));

    expect($transaction->fresh()->warranty_status)->toBe('ditolak');
});

it('gagal memproses garansi jika data wajib kosong atau status tidak sesuai', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'diajukan',
    ]);
    [$notClaimed] = testTransactionWithDetail(customerUser(), testProduct(), [
        'status' => 'diterima',
        'received_at' => now(),
        'warranty_status' => 'tidak_ada',
    ]);

    $this->actingAs($admin)
        ->from(route('admin.transactions.show', $transaction))
        ->patch(route('admin.transactions.warranty', $transaction), [])
        ->assertRedirect(route('admin.transactions.show', $transaction))
        ->assertSessionHasErrors(['decision', 'refund_amount']);

    $this->actingAs($admin)
        ->from(route('admin.transactions.show', $notClaimed))
        ->patch(route('admin.transactions.warranty', $notClaimed), [
            'decision' => 'diterima',
            'refund_amount' => 0,
        ])
        ->assertRedirect(route('admin.transactions.show', $notClaimed))
        ->assertSessionHasErrors('warranty');
});
