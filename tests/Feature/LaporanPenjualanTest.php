<?php

use App\Exports\ReportExport;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

it('mengestimasi pendapatan bulan berjalan dengan weekday-adjusted EWMA', function () {
    Carbon::setTestNow(Carbon::parse('2026-06-17 12:00:00'));

    try {
        $customer = customerUser();
        $address = testAddressFor($customer);
        $product = testProduct(['harga' => 1000]);

        foreach (range(1, 17) as $day) {
            $date = Carbon::create(2026, 6, $day, 10);
            $dailyRevenue = $date->dayOfWeekIso * 100;

            testTransactionWithDetail($customer, $product, [
                'address' => $address,
                'status' => 'selesai',
                'tanggal' => $date,
                'completed_at' => $date,
            ], [
                'quantity' => 1,
                'harga_saat_transaksi' => $dailyRevenue,
            ]);
        }

        $summary = app(ReportService::class)->build(6, 2026)['summary'];

        expect((float) $summary['net_revenue'])->toBe(6200.0)
            ->and((float) $summary['estimated_revenue'])->toBe(11500.0);
    } finally {
        Carbon::setTestNow();
    }
});

it('mengisi jumlah terjual dan total omzet pada sheet produk export laporan', function () {
    $customer = customerUser();
    $product = testProduct(['nama_produk' => 'Produk Export', 'harga' => 15000, 'stok' => 1]);

    testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'tanggal' => now(),
        'completed_at' => now(),
    ], [
        'quantity' => 3,
        'harga_saat_transaksi' => 15000,
    ]);

    $report = app(ReportService::class)->build(now()->month, now()->year);
    $productSheet = (new ReportExport($report))->sheets()[3]->array();
    $productRow = collect($productSheet)->first(fn (array $row) => $row[0] === 'Produk Export');

    expect($productRow)->not->toBeNull()
        ->and($productRow[1])->toBe(3)
        ->and((float) $productRow[2])->toBe(45000.0);
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
