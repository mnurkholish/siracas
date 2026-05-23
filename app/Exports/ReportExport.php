<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    public function __construct(private readonly array $report)
    {
    }

    public function sheets(): array
    {
        return [
            new ReportSheet('Ringkasan', $this->summarySheet()),
            new ReportSheet('Penjualan Harian', $this->dailySalesSheet()),
            new ReportSheet('Penjualan Bulanan', $this->monthlySalesSheet()),
            new ReportSheet('Produk', $this->productSheet()),
        ];
    }

    private function summarySheet(): array
    {
        $summary = $this->report['summary'];
        $customers = $this->report['customerStats'];

        return [
            ['Periode laporan', $this->report['period']['label']],
            ['Total pendapatan bulan ini', $summary['net_revenue']],
            ['Total transaksi bulan ini', $summary['transaction_count']],
            ['Total produk terjual bulan ini', $summary['products_sold']],
            ['Total refund/garansi bulan ini', $summary['refund_total']],
            ['Total customer', $customers['total']],
            ['Customer baru bulan ini', $customers['new']],
            ['Customer aktif bulan ini', $customers['active']],
            ['Estimasi pendapatan bulan ini', $summary['estimated_revenue']],
        ];
    }

    private function dailySalesSheet(): array
    {
        return $this->report['dailySales']
            ->map(fn (array $row) => [
                $row['date'],
                $row['transaction_count'],
                $row['products_sold'],
                $row['product_revenue'],
                $row['shipping_revenue'],
                $row['refund_total'],
                $row['net_revenue'],
            ])
            ->prepend([
                'Tanggal',
                'Jumlah transaksi',
                'Jumlah produk terjual',
                'Pendapatan produk',
                'Ongkir',
                'Refund/garansi',
                'Total pendapatan bersih',
            ])
            ->values()
            ->all();
    }

    private function monthlySalesSheet(): array
    {
        return $this->report['monthlySales']
            ->map(fn (array $row) => [
                $row['label'],
                $row['transaction_count'],
                $row['products_sold'],
                $row['product_revenue'],
                $row['shipping_revenue'],
                $row['refund_total'],
                $row['net_revenue'],
            ])
            ->prepend([
                'Bulan',
                'Jumlah transaksi',
                'Jumlah produk terjual',
                'Pendapatan produk',
                'Ongkir',
                'Refund/garansi',
                'Total pendapatan bersih',
            ])
            ->values()
            ->all();
    }

    private function productSheet(): array
    {
        $stats = $this->report['productStats'];
        $products = collect()
            ->merge($stats['bestSelling'])
            ->merge($stats['highestRevenue'])
            ->merge($stats['lowStock'])
            ->merge($stats['lowRating'])
            ->keyBy(fn ($product) => (int) data_get($product, 'id', 0))
            ->filter(fn ($product, int $id) => $id > 0);

        return $products
            ->map(function ($product) use ($stats) {
                $id = (int) data_get($product, 'id');
                $notes = collect([
                    $this->containsProduct($stats['bestSelling'], $id) ? 'Terlaris' : null,
                    $this->containsProduct($stats['highestRevenue'], $id) ? 'Omzet Tertinggi' : null,
                    $this->containsProduct($stats['lowStock'], $id) ? 'Stok Rendah' : null,
                    $this->containsProduct($stats['lowRating'], $id) ? 'Rating Rendah' : null,
                ])->filter()->implode(', ');

                return [
                    data_get($product, 'name', '-'),
                    (int) data_get($product, 'quantity_sold', 0),
                    (float) data_get($product, 'total_revenue', 0),
                    (int) data_get($product, 'stock', 0),
                    data_get($product, 'average_rating'),
                    (int) data_get($product, 'reviews_count', 0),
                    $notes !== '' ? $notes : 'Normal',
                ];
            })
            ->prepend([
                'Nama produk',
                'Jumlah terjual',
                'Total omzet',
                'Stok saat ini',
                'Rata-rata rating',
                'Jumlah review',
                'Catatan',
            ])
            ->values()
            ->all();
    }

    private function containsProduct($products, int $id): bool
    {
        return collect($products)->contains(fn ($product) => (int) data_get($product, 'id', 0) === $id);
    }
}
