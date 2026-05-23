<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public const VALID_TRANSACTION_STATUSES = [
        'dibayar',
        'diproses',
        'dikirim',
        'diterima',
        'selesai',
    ];

    public const STATUS_LABELS = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'diterima' => 'Diterima',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'kedaluwarsa' => 'Kedaluwarsa',
    ];

    public function build(int|string|null $month = null, int|string|null $year = null): array
    {
        $period = $this->period($month, $year);
        $dailySales = $this->dailySales($period);
        $monthlySales = $this->monthlySales($period['year']);
        $bestSellingProducts = $this->bestSellingProducts($period);
        $highestRevenueProducts = $this->highestRevenueProducts($period);
        $lowStockProducts = $this->lowStockProducts();
        $lowRatingProducts = $this->lowRatingProducts();
        $statusCounts = $this->statusCounts($period);
        $summary = $this->summary($period, $dailySales);
        $customerStats = $this->customerStats($period);

        return [
            'period' => $period,
            'validStatuses' => self::VALID_TRANSACTION_STATUSES,
            'statusLabels' => self::STATUS_LABELS,
            'summary' => $summary,
            'dailySales' => $dailySales,
            'monthlySales' => $monthlySales,
            'statusCounts' => $statusCounts,
            'productStats' => [
                'bestSelling' => $bestSellingProducts,
                'highestRevenue' => $highestRevenueProducts,
                'lowStock' => $lowStockProducts,
                'lowRating' => $lowRatingProducts,
            ],
            'customerStats' => $customerStats,
            'charts' => [
                'dailySales' => [
                    'labels' => $dailySales->pluck('label')->all(),
                    'transactions' => $dailySales->pluck('transaction_count')->all(),
                    'revenue' => $dailySales->pluck('net_revenue')->all(),
                ],
                'monthlySales' => [
                    'labels' => $monthlySales->pluck('label')->all(),
                    'revenue' => $monthlySales->pluck('net_revenue')->all(),
                ],
                'bestSellingProducts' => [
                    'labels' => $bestSellingProducts->pluck('name')->all(),
                    'quantities' => $bestSellingProducts->pluck('quantity_sold')->all(),
                ],
                'statusCounts' => [
                    'labels' => $statusCounts->pluck('label')->all(),
                    'counts' => $statusCounts->pluck('count')->all(),
                ],
            ],
        ];
    }

    private function period(int|string|null $month, int|string|null $year): array
    {
        $now = now();
        $month = (int) ($month ?: $now->month);
        $year = (int) ($year ?: $now->year);

        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = $now->year;
        }

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        return [
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'label' => $start->translatedFormat('F Y'),
            'isCurrentMonth' => $start->isSameMonth($now),
            'daysInMonth' => $start->daysInMonth,
            'elapsedDays' => $start->isSameMonth($now) ? min($now->day, $start->daysInMonth) : $start->daysInMonth,
        ];
    }

    private function summary(array $period, Collection $dailySales): array
    {
        $netRevenue = (float) $dailySales->sum('net_revenue');
        $elapsedDays = max(1, (int) $period['elapsedDays']);
        $estimatedRevenue = $period['isCurrentMonth']
            ? ($netRevenue / $elapsedDays) * (int) $period['daysInMonth']
            : $netRevenue;

        return [
            'product_revenue' => (float) $dailySales->sum('product_revenue'),
            'shipping_revenue' => (float) $dailySales->sum('shipping_revenue'),
            'net_revenue' => $netRevenue,
            'transaction_count' => (int) $dailySales->sum('transaction_count'),
            'products_sold' => (int) $dailySales->sum('products_sold'),
            'refund_total' => (float) $dailySales->sum('refund_total'),
            'estimated_revenue' => max(0, $estimatedRevenue),
        ];
    }

    private function dailySales(array $period): Collection
    {
        $transactionRows = Transaction::query()
            ->selectRaw('DATE(tanggal) as sales_date')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('COALESCE(SUM(ongkir), 0) as shipping_revenue')
            ->selectRaw('COALESCE(SUM(refund_amount), 0) as refund_total')
            ->whereIn('status', self::VALID_TRANSACTION_STATUSES)
            ->whereBetween('tanggal', [$period['start'], $period['end']])
            ->groupBy('sales_date')
            ->get()
            ->keyBy('sales_date');

        $detailRows = TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->selectRaw('DATE(transactions.tanggal) as sales_date')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity), 0) as products_sold')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity * transaction_details.harga_saat_transaksi), 0) as product_revenue')
            ->whereIn('transactions.status', self::VALID_TRANSACTION_STATUSES)
            ->whereBetween('transactions.tanggal', [$period['start'], $period['end']])
            ->groupBy('sales_date')
            ->get()
            ->keyBy('sales_date');

        return collect(range(1, (int) $period['daysInMonth']))
            ->map(function (int $day) use ($period, $transactionRows, $detailRows) {
                $date = Carbon::create((int) $period['year'], (int) $period['month'], $day);
                $key = $date->toDateString();
                $transaction = $transactionRows->get($key);
                $detail = $detailRows->get($key);
                $productRevenue = (float) ($detail->product_revenue ?? 0);
                $shippingRevenue = (float) ($transaction->shipping_revenue ?? 0);
                $refundTotal = (float) ($transaction->refund_total ?? 0);

                return [
                    'date' => $key,
                    'label' => $date->format('d M'),
                    'transaction_count' => (int) ($transaction->transaction_count ?? 0),
                    'products_sold' => (int) ($detail->products_sold ?? 0),
                    'product_revenue' => $productRevenue,
                    'shipping_revenue' => $shippingRevenue,
                    'refund_total' => $refundTotal,
                    'net_revenue' => max(0, $productRevenue + $shippingRevenue - $refundTotal),
                ];
            });
    }

    private function monthlySales(int $year): Collection
    {
        $transactionRows = Transaction::query()
            ->selectRaw($this->monthExpression('tanggal').' as sales_month')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('COALESCE(SUM(ongkir), 0) as shipping_revenue')
            ->selectRaw('COALESCE(SUM(refund_amount), 0) as refund_total')
            ->whereIn('status', self::VALID_TRANSACTION_STATUSES)
            ->whereYear('tanggal', $year)
            ->groupBy('sales_month')
            ->get()
            ->keyBy('sales_month');

        $detailRows = TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->selectRaw($this->monthExpression('transactions.tanggal').' as sales_month')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity), 0) as products_sold')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity * transaction_details.harga_saat_transaksi), 0) as product_revenue')
            ->whereIn('transactions.status', self::VALID_TRANSACTION_STATUSES)
            ->whereYear('transactions.tanggal', $year)
            ->groupBy('sales_month')
            ->get()
            ->keyBy('sales_month');

        return collect(range(1, 12))->map(function (int $month) use ($year, $transactionRows, $detailRows) {
            $date = Carbon::create($year, $month, 1);
            $transaction = $transactionRows->get($month);
            $detail = $detailRows->get($month);
            $productRevenue = (float) ($detail->product_revenue ?? 0);
            $shippingRevenue = (float) ($transaction->shipping_revenue ?? 0);
            $refundTotal = (float) ($transaction->refund_total ?? 0);

            return [
                'month' => $month,
                'label' => $date->translatedFormat('M'),
                'transaction_count' => (int) ($transaction->transaction_count ?? 0),
                'products_sold' => (int) ($detail->products_sold ?? 0),
                'product_revenue' => $productRevenue,
                'shipping_revenue' => $shippingRevenue,
                'refund_total' => $refundTotal,
                'net_revenue' => max(0, $productRevenue + $shippingRevenue - $refundTotal),
            ];
        });
    }

    private function statusCounts(array $period): Collection
    {
        $rows = Transaction::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->whereBetween('tanggal', [$period['start'], $period['end']])
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(Transaction::STATUSES)
            ->map(fn (string $status) => [
                'status' => $status,
                'label' => self::STATUS_LABELS[$status] ?? str($status)->headline()->toString(),
                'count' => (int) ($rows[$status] ?? 0),
            ])
            ->values();
    }

    private function bestSellingProducts(array $period): Collection
    {
        return $this->productSalesQuery($period)
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();
    }

    private function highestRevenueProducts(array $period): Collection
    {
        return $this->productSalesQuery($period)
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
    }

    private function productSalesQuery(array $period)
    {
        return TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->selectRaw('products.id, products.nama_produk as name, products.stok as stock')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity), 0) as quantity_sold')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity * transaction_details.harga_saat_transaksi), 0) as total_revenue')
            ->whereIn('transactions.status', self::VALID_TRANSACTION_STATUSES)
            ->whereBetween('transactions.tanggal', [$period['start'], $period['end']])
            ->groupBy('products.id', 'products.nama_produk', 'products.stok');
    }

    private function lowStockProducts(): Collection
    {
        return Product::query()
            ->select('id', 'nama_produk as name', 'stok as stock')
            ->orderBy('stok')
            ->orderBy('nama_produk')
            ->limit(5)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => (int) $product->stock,
            ]);
    }

    private function lowRatingProducts(): Collection
    {
        return Product::query()
            ->join('reviews', 'reviews.product_id', '=', 'products.id')
            ->selectRaw('products.id, products.nama_produk as name, products.stok as stock')
            ->selectRaw('ROUND(AVG(reviews.rating), 1) as average_rating')
            ->selectRaw('COUNT(reviews.id) as reviews_count')
            ->groupBy('products.id', 'products.nama_produk', 'products.stok')
            ->orderBy('average_rating')
            ->orderByDesc('reviews_count')
            ->limit(5)
            ->get();
    }

    private function customerStats(array $period): array
    {
        $totalCustomers = User::query()
            ->where('role', 'customer')
            ->count();

        $newCustomers = User::query()
            ->where('role', 'customer')
            ->whereBetween('created_at', [$period['start'], $period['end']])
            ->count();

        $activeCustomers = Transaction::query()
            ->whereIn('status', self::VALID_TRANSACTION_STATUSES)
            ->whereBetween('tanggal', [$period['start'], $period['end']])
            ->distinct('user_id')
            ->count('user_id');

        $detailTotals = TransactionDetail::query()
            ->select('transaction_id')
            ->selectRaw('SUM(quantity * harga_saat_transaksi) as product_revenue')
            ->groupBy('transaction_id');

        $topCustomers = Transaction::query()
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->leftJoinSub($detailTotals, 'detail_totals', 'detail_totals.transaction_id', '=', 'transactions.id')
            ->selectRaw('users.id, users.username as name')
            ->selectRaw('COUNT(transactions.id) as transaction_count')
            ->selectRaw('COALESCE(SUM(detail_totals.product_revenue), 0) + COALESCE(SUM(transactions.ongkir), 0) - COALESCE(SUM(transactions.refund_amount), 0) as total_spent')
            ->where('users.role', 'customer')
            ->whereIn('transactions.status', self::VALID_TRANSACTION_STATUSES)
            ->whereBetween('transactions.tanggal', [$period['start'], $period['end']])
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        return [
            'total' => $totalCustomers,
            'new' => $newCustomers,
            'active' => $activeCustomers,
            'topBuyers' => $topCustomers,
        ];
    }

    private function monthExpression(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "CAST(strftime('%m', {$column}) as integer)";
        }

        return "MONTH({$column})";
    }
}
