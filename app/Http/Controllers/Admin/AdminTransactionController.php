<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminTransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::query()
            ->with('user')
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10);

        return view('admin.transactions.index', compact('transactions'));
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
        $validated = $request->validate([
            'status' => ['required', Rule::in(Transaction::STATUSES)],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $transaction->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.transactions.show', $transaction)
            ->with('success', 'Status telah diperbarui');
    }
}
