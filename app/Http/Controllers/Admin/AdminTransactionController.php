<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminTransactionController extends Controller
{
    private const ADMIN_STATUS_TRANSITIONS = [
        'dibayar' => ['diproses'],
        'diproses' => ['dikirim'],
        'dikirim' => ['selesai'],
    ];

    private const STATUS_LABELS = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'kedaluwarsa' => 'Kedaluwarsa',
    ];

    public function index(Request $request)
    {
        $statusOptions = $this->statusOptions(Transaction::ACTIVE_STATUSES);
        $transactions = $this->filteredTransactions($request, Transaction::ACTIVE_STATUSES)
            ->paginate(10)
            ->withQueryString();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'title' => 'Transaksi',
            'subtitle' => 'Kelola pesanan customer yang masih aktif.',
            'emptyMessage' => 'Belum ada transaksi aktif.',
            'statusOptions' => $statusOptions,
            'resetRoute' => route('admin.transactions.index'),
        ]);
    }

    public function history(Request $request)
    {
        $statusOptions = $this->statusOptions(Transaction::HISTORY_STATUSES);
        $transactions = $this->filteredTransactions($request, Transaction::HISTORY_STATUSES)
            ->paginate(10)
            ->withQueryString();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'title' => 'Riwayat Transaksi',
            'subtitle' => 'Daftar riwayat transaksi.',
            'emptyMessage' => 'Belum ada riwayat transaksi.',
            'statusOptions' => $statusOptions,
            'resetRoute' => route('admin.transactions.history'),
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load([
            'user',
            'transactionDetails.product',
            'address.kecamatan.kota.provinsi',
        ]);

        return view('admin.transactions.show', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $allowedStatuses = self::ADMIN_STATUS_TRANSITIONS[$transaction->status] ?? [];

        if ($allowedStatuses === []) {
            return back()->withErrors([
                'status' => 'Status transaksi belum bisa diubah',
            ]);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status transaksi belum bisa diubah',
        ]);

        $transaction->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'Status telah diperbarui');
    }

    private function filteredTransactions(Request $request, array $allowedStatuses)
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');
        $tanggal = $this->dateQuery($request->query('tanggal'));

        return Transaction::query()
            ->with('user')
            ->whereIn('status', $allowedStatuses)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->whereHas('user', function ($query) use ($search) {
                            $query->where('username', 'like', "%{$search}%");
                        })
                        ->orWhereHas('transactionDetails.product', function ($query) use ($search) {
                            $query->where('nama_produk', 'like', "%{$search}%");
                        });
                });
            })
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($tanggal, function ($query) use ($tanggal) {
                $query->whereDate('tanggal', $tanggal);
            })
            ->latest('tanggal')
            ->latest('id');
    }

    private function statusOptions(array $statuses): array
    {
        return collect($statuses)
            ->mapWithKeys(fn ($status) => [$status => self::STATUS_LABELS[$status] ?? $status])
            ->all();
    }

    private function dateQuery(mixed $value): ?string
    {
        $value = (string) $value;

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }
}
