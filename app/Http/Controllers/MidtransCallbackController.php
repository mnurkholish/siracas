<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MidtransCallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'payment_type' => ['nullable', 'string'],
        ]);

        $serverKey = config('services.midtrans.server_key');
        $signature = hash(
            'sha512',
            $validated['order_id'].$validated['status_code'].$validated['gross_amount'].$serverKey
        );

        if (! $serverKey || ! hash_equals($signature, $validated['signature_key'])) {
            return response()->json([
                'message' => 'Invalid signature.',
            ], Response::HTTP_FORBIDDEN);
        }

        $transaction = Transaction::query()
            ->where('order_id', $validated['order_id'])
            ->first();

        if (! $transaction) {
            return response()->json([
                'message' => 'Transaction not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $status = match ($validated['transaction_status']) {
            'settlement', 'capture' => 'dibayar',
            'pending' => 'menunggu_pembayaran',
            'cancel', 'deny' => 'dibatalkan',
            'expire' => 'kedaluwarsa',
            default => null,
        };

        if (! $status) {
            return response()->json([
                'message' => 'Transaction status ignored.',
            ]);
        }

        $transaction->update([
            'status' => $status,
            'payment_type' => $validated['payment_type'] ?? $transaction->payment_type,
            'paid_at' => $status === 'dibayar' ? ($transaction->paid_at ?? now()) : $transaction->paid_at,
        ]);

        return response()->json([
            'message' => 'Callback processed.',
        ]);
    }
}
