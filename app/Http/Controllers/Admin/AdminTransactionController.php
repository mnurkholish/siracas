<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Notifications\OrderCompletedNotification;
use App\Notifications\OrderShippedNotification;
use App\Notifications\ShippingCostUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminTransactionController extends Controller
{
    private const ADMIN_STATUS_TRANSITIONS = [
        'dibayar' => ['diproses'],
        'diproses' => ['dikirim'],
        'dikirim' => ['diterima'],
    ];

    private const STATUS_LABELS = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'diterima' => 'Diterima',
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
            'subtitle' => 'Daftar pesanan yang perlu ditangani.',
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

        $previousStatus = $transaction->status;

        $transaction->update([
            'status' => $validated['status'],
            'received_at' => $validated['status'] === 'diterima' ? ($transaction->received_at ?? now()) : $transaction->received_at,
        ]);

        if ($previousStatus !== $validated['status']) {
            $transaction->loadMissing('user');

            match ($validated['status']) {
                'dikirim' => $transaction->user?->notify(new OrderShippedNotification($transaction)),
                default => null,
            };
        }

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'Status telah diperbarui');
    }

    public function updateOngkir(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'menunggu_pembayaran') {
            return back()->withErrors([
                'ongkir' => 'Ongkir hanya dapat diubah saat transaksi menunggu pembayaran.',
            ]);
        }

        $validated = $request->validate([
            'ongkir' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ], [
            'ongkir.required' => 'Ongkir wajib diisi.',
            'ongkir.numeric' => 'Ongkir harus berupa angka.',
            'ongkir.min' => 'Ongkir tidak boleh kurang dari 0.',
            'ongkir.max' => 'Ongkir terlalu besar.',
        ]);

        $previousOngkir = (float) $transaction->ongkir;
        $newOngkir = (float) $validated['ongkir'];

        if (abs($previousOngkir - $newOngkir) < 0.01) {
            return $this->noChangesResponse();
        }

        $transaction->update([
            'ongkir' => $validated['ongkir'],
            'order_id' => null,
            'snap_token' => null,
        ]);

        if ($newOngkir > 0 && $previousOngkir !== $newOngkir) {
            $transaction->loadMissing('user');
            $transaction->user?->notify(new ShippingCostUpdatedNotification($transaction));
        }

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'Ongkir telah diperbarui');
    }

    public function updateRefund(Request $request, Transaction $transaction)
    {
        return back()->withErrors([
            'warranty' => 'Pengembalian hanya dapat disimpan melalui proses terima/tolak garansi.',
        ]);
    }

    public function processWarranty(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'diterima' || $transaction->warranty_status !== 'diajukan') {
            return back()->withErrors([
                'warranty' => 'Garansi hanya dapat diproses saat transaksi diterima dan garansi sedang diajukan.',
            ]);
        }

        $maxRefund = $transaction->totalAkhir();

        $validated = $request->validate([
            'decision' => ['required', Rule::in(['diterima', 'ditolak'])],
            'refund_amount' => ['required', 'numeric', 'min:0', 'max:'.$maxRefund],
            'refund_note' => ['nullable', 'string', 'max:2000'],
        ], [
            'decision.required' => 'Keputusan garansi wajib dipilih.',
            'decision.in' => 'Keputusan garansi tidak valid.',
            'refund_amount.required' => 'Nominal pengembalian wajib diisi.',
            'refund_amount.numeric' => 'Nominal pengembalian harus berupa angka.',
            'refund_amount.min' => 'Nominal pengembalian tidak boleh kurang dari 0.',
            'refund_amount.max' => 'Nominal pengembalian tidak boleh lebih besar dari total pembayaran transaksi.',
            'refund_note.max' => 'Catatan pengembalian maksimal 2000 karakter.',
        ]);

        $refundAmount = (float) $validated['refund_amount'];
        $refundNote = trim((string) ($validated['refund_note'] ?? ''));

        if ($validated['decision'] === 'ditolak') {
            if ($refundNote === '') {
                return back()->withErrors([
                    'refund_note' => 'Alasan penolakan wajib diisi.',
                ])->withInput();
            }

            $refundAmount = 0;
        }

        if ($validated['decision'] === 'diterima') {
            if ($refundAmount <= 0) {
                return back()->withErrors([
                    'refund_amount' => 'Nominal pengembalian wajib lebih dari 0 jika garansi diterima.',
                ])->withInput();
            }

            if ($refundNote === '') {
                return back()->withErrors([
                    'refund_note' => 'Catatan pengembalian wajib diisi jika garansi diterima.',
                ])->withInput();
            }
        }

        $transaction->update([
            'status' => 'selesai',
            'completed_at' => $transaction->completed_at ?? now(),
            'warranty_status' => $validated['decision'],
            'warranty_resolved_at' => now(),
            'refund_amount' => $refundAmount,
            'refund_note' => $refundNote !== '' ? $refundNote : null,
            'refunded_at' => $refundAmount > 0 ? ($transaction->refunded_at ?? now()) : null,
        ]);

        $transaction->loadMissing('user');
        $transaction->user?->notify(new OrderCompletedNotification($transaction));

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'Garansi telah diproses dan transaksi diselesaikan.');
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
