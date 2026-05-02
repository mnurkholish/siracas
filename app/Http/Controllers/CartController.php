<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index()
    {
        $cart = Auth::user()
            ->cart()
            ->with(['cartItems.product'])
            ->first();

        return view('customer.cart.index', [
            'cart' => $cart,
            'cartItems' => $cart?->cartItems ?? collect(),
        ]);
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $product->stok],
        ], [
            'quantity.required' => 'Quantity wajib diisi.',
            'quantity.integer' => 'Quantity harus berupa angka.',
            'quantity.min' => 'Quantity minimal 1.',
            'quantity.max' => 'Quantity tidak boleh melebihi stok product.',
        ]);

        $cart = Auth::user()->cart()->firstOrCreate([]);
        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();
        $quantity = (int) $validated['quantity'];
        $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

        if ($newQuantity > $product->stok) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity tidak boleh melebihi stok product.',
            ]);
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQuantity,
            ]);
        } else {
            $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'harga_saat_dimasukkan' => $product->harga,
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $cartItem->load('product');

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:' . $cartItem->product->stok],
        ], [
            'quantity.required' => 'Quantity wajib diisi.',
            'quantity.integer' => 'Quantity harus berupa angka.',
            'quantity.min' => 'Quantity minimal 0.',
            'quantity.max' => 'Quantity tidak boleh melebihi stok product.',
        ]);

        if ((int) $validated['quantity'] === 0) {
            $cartItem->delete();

            return redirect()
                ->route('cart.index');
        }

        $cartItem->update([
            'quantity' => (int) $validated['quantity'],
        ]);

        return redirect()
            ->route('cart.index');
    }

    public function destroy(CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $cartItem->delete();

        return redirect()
            ->route('cart.index');
    }

    private function authorizeCartItem(CartItem $cartItem): void
    {
        $cartItem->loadMissing('cart');

        abort_unless($cartItem->cart->user_id === Auth::id(), 403);
    }
}
