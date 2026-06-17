<?php

use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin bisa melihat daftar dan detail riwayat transaksi', function () {
    $admin = adminUser();
    [$transaction] = testTransactionWithDetail(customerUser(), testProduct(), [
        'status' => 'selesai',
        'completed_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.transactions.history'))
        ->assertOk()
        ->assertViewHas('transactions');

    $this->actingAs($admin)
        ->get(route('admin.transactions.show', $transaction))
        ->assertOk()
        ->assertViewHas('transaction');
});

it('customer bisa melihat riwayat transaksinya sendiri dan bukan transaksi customer lain', function () {
    $owner = customerUser();
    $otherCustomer = customerUser();
    [$ownerTransaction] = testTransactionWithDetail($owner, testProduct(), ['status' => 'selesai', 'completed_at' => now()]);
    [$otherTransaction] = testTransactionWithDetail($otherCustomer, testProduct(), ['status' => 'selesai', 'completed_at' => now()]);

    $this->actingAs($owner)
        ->get(route('transactions.history'))
        ->assertOk()
        ->assertViewHas('transactions', function ($transactions) use ($ownerTransaction, $otherTransaction) {
            return $transactions->contains('id', $ownerTransaction->id)
                && ! $transactions->contains('id', $otherTransaction->id);
        });
});

it('riwayat hanya berisi transaksi dengan status selesai dibatalkan atau kedaluwarsa', function () {
    $customer = customerUser();
    [$activeTransaction] = testTransactionWithDetail($customer, testProduct(), ['status' => 'dikirim']);
    [$historyTransaction] = testTransactionWithDetail($customer, testProduct(), ['status' => 'dibatalkan']);

    $this->actingAs($customer)
        ->get(route('transactions.history'))
        ->assertOk()
        ->assertViewHas('transactions', function ($transactions) use ($activeTransaction, $historyTransaction) {
            return $transactions->contains('id', $historyTransaction->id)
                && ! $transactions->contains('id', $activeTransaction->id);
        });
});

it('menghitung total bayar refund garansi dan pendapatan bersih', function () {
    $customer = customerUser();
    $product = testProduct(['harga' => 10000]);

    [$transaction] = testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'tanggal' => now(),
        'completed_at' => now(),
        'ongkir' => 5000,
        'refund_amount' => 2000,
        'warranty_status' => 'diterima',
    ], [
        'quantity' => 2,
        'harga_saat_transaksi' => 10000,
    ]);

    $transaction->load('transactionDetails');

    expect($transaction->totalAkhir())->toBe(25000.0)
        ->and($transaction->pendapatanBersih())->toBe(23000.0);

    $summary = app(ReportService::class)->build(now()->month, now()->year)['summary'];

    expect((float) $summary['refund_total'])->toBe(2000.0)
        ->and((float) $summary['net_revenue'])->toBe(23000.0);
});
