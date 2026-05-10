<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(Request $request, Transaction $transaction, TransactionDetail $transactionDetail)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);
        abort_unless($transactionDetail->transaction_id === $transaction->id, 404);

        if ($transaction->status !== 'selesai') {
            throw ValidationException::withMessages([
                'status' => 'Review hanya dapat dibuat setelah transaksi selesai.',
            ]);
        }

        $validated = $request->validate([
            'isi' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'isi.required' => 'Isi review wajib diisi.',
            'isi.max' => 'Isi review maksimal 2000 karakter.',
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating tidak valid.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 5.',
            'foto.image' => 'Foto review harus berupa gambar.',
            'foto.mimes' => 'Foto review harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto review maksimal 2 MB.',
        ]);

        $alreadyReviewed = Review::query()
            ->where('user_id', Auth::id())
            ->where('product_id', $transactionDetail->product_id)
            ->exists();

        if ($alreadyReviewed) {
            throw ValidationException::withMessages([
                'review' => 'Produk ini sudah direview.',
            ]);
        }

        $validated['foto'] = $request->file('foto')?->store('reviews', 'public');

        Review::create([
            'user_id' => Auth::id(),
            'product_id' => $transactionDetail->product_id,
            'isi' => $validated['isi'],
            'rating' => $validated['rating'],
            'foto' => $validated['foto'],
        ]);

        return back()->with('success', 'Review berhasil ditambahkan.');
    }
}
