<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ReportService;

uses(RefreshDatabase::class);

it('menghitung total laporan penjualan dari status transaksi yang valid', function () {
    $customer = customerUser();
    $soldProduct = testProduct(['nama_produk' => 'Produk Terjual', 'harga' => 10000, 'stok' => 5]);
    $cancelledProduct = testProduct(['nama_produk' => 'Produk Batal', 'harga' => 50000, 'stok' => 5]);

    testTransactionWithDetail($customer, $soldProduct, [
        'status' => 'selesai',
        'tanggal' => now(),
        'completed_at' => now(),
        'ongkir' => 5000,
        'refund_amount' => 2000,
    ], [
        'quantity' => 2,
        'harga_saat_transaksi' => 10000,
    ]);

    testTransactionWithDetail($customer, $cancelledProduct, [
        'status' => 'dibatalkan',
        'tanggal' => now(),
        'ongkir' => 10000,
    ], [
        'quantity' => 5,
        'harga_saat_transaksi' => 50000,
    ]);

    $report = app(ReportService::class)->build(now()->month, now()->year);
    $summary = $report['summary'];

    expect($summary['transaction_count'])->toBe(1)
        ->and($summary['products_sold'])->toBe(2)
        ->and((float) $summary['product_revenue'])->toBe(20000.0)
        ->and((float) $summary['shipping_revenue'])->toBe(5000.0)
        ->and((float) $summary['refund_total'])->toBe(2000.0)
        ->and((float) $summary['net_revenue'])->toBe(23000.0);
});

it('mencegah customer mengakses laporan penjualan', function () {
    $this->actingAs(customerUser())
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

it('mengalihkan guest dari laporan penjualan', function () {
    $this->get(route('admin.reports.index'))
        ->assertRedirect(route('login'));
});
