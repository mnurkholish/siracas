<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function checkoutForm()
    {
        $cart = Auth::user()
            ->cart()
            ->with(['cartItems.product'])
            ->first();

        $cartItems = $cart?->cartItems ?? collect();
        $addresses = $this->userAddresses();

        return view('customer.checkout.index', [
            'cart' => $cart,
            'cartItems' => $cartItems,
            'addresses' => $addresses,
        ]);
    }

    public function checkoutProcess(Request $request)
    {
        $validated = $request->validate($this->transactionRules(), $this->transactionMessages());

        $cart = Auth::user()
            ->cart()
            ->with(['cartItems.product'])
            ->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang tidak boleh kosong.',
            ]);
        }

        DB::transaction(function () use ($cart, $validated) {
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'address_id' => $validated['address_id'],
                'tanggal' => now()->toDateString(),
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($cart->cartItems as $cartItem) {
                $product = Product::query()
                    ->whereKey($cartItem->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$product || $product->stok < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$cartItem->product?->nama_produk} tidak mencukupi.",
                    ]);
                }

                $transaction->transactionDetails()->create([
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'harga_saat_transaksi' => $product->harga,
                ]);

                $product->decrement('stok', $cartItem->quantity);
            }

            $cart->cartItems()->delete();
        });

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Pesanan berhasil!');
    }

    public function buyNowForm(Product $product)
    {
        $addresses = $this->userAddresses();
        $quantity = min(max((int) request('quantity', 1), 1), max($product->stok, 1));

        return view('customer.checkout.buy-now', [
            'product' => $product,
            'addresses' => $addresses,
            'quantity' => $quantity,
        ]);
    }

    public function buyNowProcess(Request $request, Product $product)
    {
        $validated = $request->validate([
            ...$this->transactionRules(),
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            ...$this->transactionMessages(),
            'quantity.required' => 'Quantity wajib diisi.',
            'quantity.integer' => 'Quantity harus berupa angka.',
            'quantity.min' => 'Quantity minimal 1.',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedProduct->stok < (int) $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity tidak boleh melebihi stok produk.',
                ]);
            }

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'address_id' => $validated['address_id'],
                'tanggal' => now()->toDateString(),
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'pending',
            ]);

            $transaction->transactionDetails()->create([
                'product_id' => $lockedProduct->id,
                'quantity' => (int) $validated['quantity'],
                'harga_saat_transaksi' => $lockedProduct->harga,
            ]);

            $lockedProduct->decrement('stok', (int) $validated['quantity']);
        });

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Pesanan berhasil!');
    }

    public function index()
    {
        $transactions = Auth::user()
            ->transactions()
            ->with(['transactionDetails.product'])
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10);

        return view('customer.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $this->authorizeCustomerTransaction($transaction);

        $transaction->load(['transactionDetails.product', 'address.kecamatan.kota.provinsi']);

        return view('customer.transactions.show', compact('transaction'));
    }

    public function cancel(Transaction $transaction)
    {
        $this->authorizeCustomerTransaction($transaction);

        if ($transaction->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Hanya transaksi pending yang dapat dibatalkan.',
            ]);
        }

        DB::transaction(function () use ($transaction) {
            $transaction->load('transactionDetails');

            $transaction->update([
                'status' => 'cancelled',
            ]);

            foreach ($transaction->transactionDetails as $detail) {
                Product::withTrashed()
                    ->whereKey($detail->product_id)
                    ->lockForUpdate()
                    ->increment('stok', $detail->quantity);
            }
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Pesanan berhasil dibatalkan');
    }

    private function transactionRules(): array
    {
        return [
            'address_id' => [
                'required',
                'integer',
                Rule::exists('addresses', 'id')->where('user_id', Auth::id()),
            ],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function transactionMessages(): array
    {
        return [
            'address_id.required' => 'Alamat wajib dipilih.',
            'address_id.exists' => 'Alamat tidak valid.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    private function userAddresses()
    {
        return Address::query()
            ->where('user_id', Auth::id())
            ->with('kecamatan.kota.provinsi')
            ->latest()
            ->get();
    }

    private function authorizeCustomerTransaction(Transaction $transaction): void
    {
        abort_unless($transaction->user_id === Auth::id(), 403);
    }
}
