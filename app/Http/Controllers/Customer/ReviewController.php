<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function create()
    {
        $details = $this->reviewableDetailsQuery()
            ->latest('id')
            ->get()
            ->values();

        return view('customer.reviews.create', compact('details'));
    }

    public function show(TransactionDetail $transactionDetail)
    {
        $transactionDetail = $this->reviewableDetailsQuery()
            ->whereKey($transactionDetail->id)
            ->firstOrFail();

        return view('customer.reviews.show', [
            'detail' => $transactionDetail,
        ]);
    }

    public function index()
    {
        $reviews = Review::query()
            ->with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.reviews.index', compact('reviews'));
    }

    public function edit(Review $review)
    {
        $this->authorizeReviewOwner($review);

        if (! $this->reviewIsEditable($review)) {
            return redirect()
                ->route('reviews.index')
                ->with('error_alert', 'Review hanya dapat diedit maksimal 7 hari setelah dibuat.');
        }

        $review->load('product');

        return view('customer.reviews.edit', compact('review'));
    }

    public function store(Request $request, Transaction $transaction, TransactionDetail $transactionDetail)
    {
        abort_unless($transaction->user_id === Auth::id(), 403);
        abort_unless($transactionDetail->transaction_id === $transaction->id, 404);

        if ($transaction->status !== 'selesai') {
            throw ValidationException::withMessages([
                'status' => 'Review hanya dapat dibuat setelah transaksi selesai.',
            ]);
        }

        if (! $transaction->completed_at || $transaction->completed_at->lt(now()->subDays(20))) {
            throw ValidationException::withMessages([
                'status' => 'Masa penilaian produk sudah berakhir.',
            ]);
        }

        $validated = $request->validate($this->reviewRules(), $this->reviewMessages());

        $alreadyReviewed = Review::query()
            ->where('user_id', Auth::id())
            ->where('transaction_detail_id', $transactionDetail->id)
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
            'transaction_detail_id' => $transactionDetail->id,
            'isi' => $validated['isi'] ?? '',
            'rating' => $validated['rating'],
            'foto' => $validated['foto'],
        ]);

        return redirect()
            ->route('review')
            ->with('success', 'Penilaian berhasil ditambahkan.');
    }

    public function update(Request $request, Review $review)
    {
        $this->authorizeReviewOwner($review);
        $this->ensureReviewIsEditable($review);

        $validated = $request->validate($this->reviewRules(), $this->reviewMessages());

        $removePhoto = $request->boolean('remove_photo');

        if ($request->hasFile('foto')) {
            if ($review->foto) {
                Storage::disk('public')->delete($review->foto);
            }

            $validated['foto'] = $request->file('foto')->store('reviews', 'public');
        } elseif ($removePhoto && $review->foto) {
            Storage::disk('public')->delete($review->foto);
            $validated['foto'] = null;
        }

        $review->update([
            'isi' => $validated['isi'] ?? '',
            'rating' => $validated['rating'],
            'foto' => array_key_exists('foto', $validated) ? $validated['foto'] : $review->foto,
        ]);

        return redirect()
            ->route('reviews.index')
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    private function reviewableDetailsQuery()
    {
        return TransactionDetail::query()
            ->with(['transaction', 'product'])
            ->whereHas('transaction', function ($query) {
                $query
                    ->where('user_id', Auth::id())
                    ->where('status', 'selesai')
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>=', now()->subDays(20));
            })
            ->whereDoesntHave('reviews', function ($query) {
                $query->where('user_id', Auth::id());
            });
    }

    private function authorizeReviewOwner(Review $review): void
    {
        abort_unless($review->user_id === Auth::id(), 403);
    }

    private function ensureReviewIsEditable(Review $review): void
    {
        if (! $this->reviewIsEditable($review)) {
            throw ValidationException::withMessages([
                'review' => 'Review hanya dapat diedit maksimal 7 hari setelah dibuat.',
            ]);
        }
    }

    private function reviewIsEditable(Review $review): bool
    {
        return $review->created_at->gte(now()->subDays(7));
    }

    private function reviewRules(): array
    {
        return [
            'isi' => ['nullable', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }

    private function reviewMessages(): array
    {
        return [
            'isi.max' => 'Isi review maksimal 2000 karakter.',
            'rating.required' => 'Rating wajib dipilih.',
            'rating.integer' => 'Rating tidak valid.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 5.',
            'foto.image' => 'Foto review harus berupa gambar.',
            'foto.mimes' => 'Foto review harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto review maksimal 2 MB.',
        ];
    }
}
