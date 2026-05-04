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

    public function index()
    {
        $transactions = Transaction::query()
            ->with('user')
            ->whereIn('status', Transaction::ACTIVE_STATUSES)
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10);

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'title' => 'Transaksi',
            'subtitle' => 'Kelola pesanan customer yang masih aktif.',
            'emptyMessage' => 'Belum ada transaksi aktif.',
        ]);
    }

    public function history()
    {
        $transactions = Transaction::query()
            ->with('user')
            ->whereIn('status', Transaction::HISTORY_STATUSES)
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10);

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'title' => 'Riwayat Transaksi',
            'subtitle' => 'Daftar transaksi yang sudah final.',
            'emptyMessage' => 'Belum ada riwayat transaksi.',
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
}
