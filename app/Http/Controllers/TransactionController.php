<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\OngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function __construct(private readonly OngkirService $ongkirService)
    {
    }

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
            'addressOngkir' => $this->ongkirByAddress($addresses),
        ]);
    }

    public function checkoutProcess(Request $request)
    {
        $validated = $request->validate($this->transactionRules(), $this->transactionMessages());

        $cart = Auth::user()
            ->cart()
            ->with(['cartItems.product'])
            ->first();

        if (! $cart || $cart->cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang tidak boleh kosong.',
            ]);
        }

        $transaction = DB::transaction(function () use ($cart, $validated) {
            $address = $this->findAddressForCheckout((int) $validated['address_id']);
            $city = $address->kecamatan?->kota?->nama;
            $province = $address->kecamatan?->kota?->provinsi?->nama;
            $ongkir = $this->ongkirService->hitung($city, $province);
            $lines = [];
            $totalBarang = 0;

            foreach ($cart->cartItems as $cartItem) {
                $product = Product::query()
                    ->whereKey($cartItem->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product || $product->stok < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$cartItem->product?->nama_produk} tidak mencukupi.",
                    ]);
                }

                $lines[] = [
                    'product' => $product,
                    'quantity' => $cartItem->quantity,
                    'harga_saat_transaksi' => $product->harga,
                ];
                $totalBarang += $cartItem->quantity * (float) $product->harga;
            }

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'city' => $city,
                'province' => $province,
                'ongkir' => $ongkir['harga'],
                'total_barang' => $totalBarang,
                'total_bayar' => $ongkir['ditemukan'] ? $totalBarang + $ongkir['harga'] : null,
                'tanggal' => now()->toDateString(),
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($lines as $line) {
                $transaction->transactionDetails()->create([
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'harga_saat_transaksi' => $line['harga_saat_transaksi'],
                ]);

                $line['product']->decrement('stok', $line['quantity']);
            }

            $cart->cartItems()->delete();

            return $transaction;
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Pesanan berhasil!');
    }

    public function buyNowForm(Product $product)
    {
        $addresses = $this->userAddresses();
        $quantity = min(max((int) request('quantity', 1), 1), max($product->stok, 1));

        return view('customer.checkout.buy-now', [
            'product' => $product,
            'addresses' => $addresses,
            'addressOngkir' => $this->ongkirByAddress($addresses),
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

        $transaction = DB::transaction(function () use ($product, $validated) {
            $lockedProduct = Product::query()
                ->whereKey($product->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedProduct->stok < (int) $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity tidak boleh melebihi stok produk.',
                ]);
            }

            $address = $this->findAddressForCheckout((int) $validated['address_id']);
            $city = $address->kecamatan?->kota?->nama;
            $province = $address->kecamatan?->kota?->provinsi?->nama;
            $ongkir = $this->ongkirService->hitung($city, $province);
            $totalBarang = (int) $validated['quantity'] * (float) $lockedProduct->harga;

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'address_id' => $address->id,
                'city' => $city,
                'province' => $province,
                'ongkir' => $ongkir['harga'],
                'total_barang' => $totalBarang,
                'total_bayar' => $ongkir['ditemukan'] ? $totalBarang + $ongkir['harga'] : null,
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

            return $transaction;
        });

        return redirect()
            ->route('transactions.show', $transaction)
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

    public function pay(Transaction $transaction)
    {
        $this->authorizeCustomerTransaction($transaction);

        if ($transaction->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Hanya transaksi pending yang dapat dibayar.',
            ]);
        }

        if ($transaction->ongkir === null || $transaction->total_bayar === null) {
            throw ValidationException::withMessages([
                'ongkir' => OngkirService::PESAN_ONGKIR_TIDAK_DITEMUKAN.'.',
            ]);
        }

        $transaction->loadMissing(['transactionDetails.product', 'user']);
        $itemDetails = $transaction->transactionDetails
            ->map(fn ($detail) => [
                'id' => (string) $detail->product_id,
                'price' => (int) round((float) $detail->harga_saat_transaksi),
                'quantity' => (int) $detail->quantity,
                'name' => Str::limit($detail->product?->nama_produk ?? 'Produk', 50, ''),
            ])
            ->values();
        $itemDetails->push([
            'id' => 'ONGKIR-'.$transaction->id,
            'price' => (int) round((float) $transaction->ongkir),
            'quantity' => 1,
            'name' => 'Ongkir',
        ]);
        $grossAmount = (int) round((float) $transaction->total_bayar);

        if ($grossAmount <= 0) {
            throw ValidationException::withMessages([
                'total' => 'Total pembayaran tidak valid.',
            ]);
        }

        if ($transaction->snap_token) {
            return response()->json([
                'snap_token' => $transaction->snap_token,
            ]);
        }

        $serverKey = config('services.midtrans.server_key');

        if (! $serverKey) {
            throw ValidationException::withMessages([
                'payment' => 'Konfigurasi Midtrans belum lengkap.',
            ]);
        }

        if (! $transaction->order_id) {
            $transaction->forceFill([
                'order_id' => $this->generateOrderId($transaction),
            ])->save();
        }

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post(config('services.midtrans.snap_url'), [
                'transaction_details' => [
                    'order_id' => $transaction->order_id,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => $itemDetails->all(),
                'customer_details' => [
                    'first_name' => $transaction->user?->name ?? 'Customer',
                    'email' => $transaction->user?->email,
                ],
                'callbacks' => [
                    'finish' => route('transactions.show', $transaction),
                ],
            ]);

        if ($response->failed() || ! $response->json('token')) {
            logger()->warning('Midtrans Snap token request failed.', [
                'transaction_id' => $transaction->id,
                'response' => $response->body(),
            ]);

            throw ValidationException::withMessages([
                'payment' => 'Gagal membuat token pembayaran Midtrans.',
            ]);
        }

        $transaction->update([
            'snap_token' => $response->json('token'),
        ]);

        return response()->json([
            'snap_token' => $transaction->snap_token,
        ]);
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

    private function ongkirByAddress($addresses): array
    {
        return $addresses
            ->mapWithKeys(function (Address $address) {
                $city = $address->kecamatan?->kota?->nama;
                $province = $address->kecamatan?->kota?->provinsi?->nama;

                return [$address->id => $this->ongkirService->hitung($city, $province)];
            })
            ->all();
    }

    private function findAddressForCheckout(int $addressId): Address
    {
        return Address::query()
            ->where('user_id', Auth::id())
            ->with('kecamatan.kota.provinsi')
            ->findOrFail($addressId);
    }

    private function authorizeCustomerTransaction(Transaction $transaction): void
    {
        abort_unless($transaction->user_id === Auth::id(), 403);
    }

    private function generateOrderId(Transaction $transaction): string
    {
        do {
            $orderId = 'SIRACAS-'.$transaction->id.'-'.Str::upper(Str::random(8));
        } while (Transaction::query()->where('order_id', $orderId)->exists());

        return $orderId;
    }
}
