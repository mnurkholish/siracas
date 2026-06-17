<?php

use App\Exports\ReportExport;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('admin bisa mengakses halaman laporan dan customer tidak boleh mengakses', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertViewHas('summary')
        ->assertViewHas('period');

    $this->actingAs(customerUser())
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

it('laporan menghitung total pendapatan transaksi produk refund dan pendapatan bersih', function () {
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

    $summary = app(ReportService::class)->build(now()->month, now()->year)['summary'];

    expect($summary['transaction_count'])->toBe(1)
        ->and($summary['products_sold'])->toBe(2)
        ->and((float) $summary['product_revenue'])->toBe(20000.0)
        ->and((float) $summary['shipping_revenue'])->toBe(5000.0)
        ->and((float) $summary['refund_total'])->toBe(2000.0)
        ->and((float) $summary['net_revenue'])->toBe(23000.0);
});

it('filter bulan dan tahun laporan menghasilkan data sesuai periode', function () {
    $customer = customerUser();
    $product = testProduct(['harga' => 10000]);

    testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'tanggal' => Carbon::create(2026, 5, 10),
        'completed_at' => Carbon::create(2026, 5, 10),
    ], [
        'quantity' => 1,
        'harga_saat_transaksi' => 10000,
    ]);

    testTransactionWithDetail($customer, $product, [
        'status' => 'selesai',
        'tanggal' => Carbon::create(2026, 6, 10),
        'completed_at' => Carbon::create(2026, 6, 10),
    ], [
        'quantity' => 3,
        'harga_saat_transaksi' => 10000,
    ]);

    $may = app(ReportService::class)->build(5, 2026)['summary'];
    $june = app(ReportService::class)->build(6, 2026)['summary'];

    expect((float) $may['product_revenue'])->toBe(10000.0)
        ->and((float) $june['product_revenue'])->toBe(30000.0);
});

it('export xlsx laporan bisa dibuat dan sheet produk berisi data minimal', function () {
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

    $this->actingAs(adminUser())
        ->get(route('admin.reports.export', ['month' => now()->month, 'year' => now()->year]))
        ->assertOk();

    $report = app(ReportService::class)->build(now()->month, now()->year);
    $productSheet = (new ReportExport($report))->sheets()[3]->array();
    $productRow = collect($productSheet)->first(fn (array $row) => $row[0] === 'Produk Export');

    expect($productRow)->not->toBeNull()
        ->and($productRow[1])->toBe(3)
        ->and((float) $productRow[2])->toBe(45000.0);
});
