<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\ReportService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const ACTION_STATUSES = [
        'dibayar',
        'diproses',
    ];

    private const LOW_STOCK_LIMIT = 5;

    public function index()
    {
        $today = now()->toDateString();
        $salesChart = $this->salesChart();
        $followUpTransactions = $this->followUpTransactions();
        $lowStockProducts = Product::query()
            ->where('stok', '<=', self::LOW_STOCK_LIMIT)
            ->orderBy('stok')
            ->orderBy('nama_produk')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'summary' => [
                'todayRevenue' => $this->todayRevenue($today),
                'newTransactionsToday' => Transaction::query()
                    ->whereDate('created_at', $today)
                    ->count(),
                'ordersNeedProcess' => Transaction::query()
                    ->whereIn('status', self::ACTION_STATUSES)
                    ->count(),
                'warrantyRequests' => Transaction::query()
                    ->where('warranty_status', 'diajukan')
                    ->count(),
                'lowStockCount' => Product::query()
                    ->where('stok', '<=', self::LOW_STOCK_LIMIT)
                    ->count(),
            ],
            'salesChart' => $salesChart,
            'followUpTransactions' => $followUpTransactions,
            'lowStockProducts' => $lowStockProducts,
            'latestTransactions' => Transaction::query()
                ->with(['user', 'transactionDetails'])
                ->latest('tanggal')
                ->latest('id')
                ->limit(5)
                ->get(),
            'lowStockLimit' => self::LOW_STOCK_LIMIT,
        ]);
    }

    private function todayRevenue(string $today): float
    {
        $productRevenue = TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status', ReportService::VALID_TRANSACTION_STATUSES)
            ->whereDate('transactions.tanggal', $today)
            ->sum(DB::raw('transaction_details.quantity * transaction_details.harga_saat_transaksi'));

        $shippingAndRefund = Transaction::query()
            ->whereIn('status', ReportService::VALID_TRANSACTION_STATUSES)
            ->whereDate('tanggal', $today)
            ->selectRaw('COALESCE(SUM(ongkir), 0) as shipping_total')
            ->selectRaw('COALESCE(SUM(refund_amount), 0) as refund_total')
            ->first();

        return max(0, (float) $productRevenue + (float) $shippingAndRefund->shipping_total - (float) $shippingAndRefund->refund_total);
    }

    private function salesChart(): array
    {
        $start = now()->subDays(6)->startOfDay();
        $end = now()->endOfDay();

        $transactionRows = Transaction::query()
            ->selectRaw('DATE(tanggal) as sales_date')
            ->selectRaw('COALESCE(SUM(ongkir), 0) as shipping_total')
            ->selectRaw('COALESCE(SUM(refund_amount), 0) as refund_total')
            ->whereIn('status', ReportService::VALID_TRANSACTION_STATUSES)
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('sales_date')
            ->get()
            ->keyBy('sales_date');

        $detailRows = TransactionDetail::query()
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->selectRaw('DATE(transactions.tanggal) as sales_date')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity * transaction_details.harga_saat_transaksi), 0) as product_total')
            ->whereIn('transactions.status', ReportService::VALID_TRANSACTION_STATUSES)
            ->whereBetween('transactions.tanggal', [$start, $end])
            ->groupBy('sales_date')
            ->get()
            ->keyBy('sales_date');

        $rows = collect(range(0, 6))->map(function (int $offset) use ($start, $transactionRows, $detailRows) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();
            $transaction = $transactionRows->get($key);
            $detail = $detailRows->get($key);
            $revenue = (float) ($detail->product_total ?? 0)
                + (float) ($transaction->shipping_total ?? 0)
                - (float) ($transaction->refund_total ?? 0);

            return [
                'label' => $date->format('d M'),
                'revenue' => max(0, $revenue),
            ];
        });

        return [
            'labels' => $rows->pluck('label')->all(),
            'revenue' => $rows->pluck('revenue')->all(),
        ];
    }

    private function followUpTransactions()
    {
        return Transaction::query()
            ->with(['user', 'transactionDetails'])
            ->where(function ($query) {
                $query
                    ->whereIn('status', self::ACTION_STATUSES)
                    ->orWhere('warranty_status', 'diajukan');
            })
            ->latest('tanggal')
            ->latest('id')
            ->limit(5)
            ->get();
    }
}
