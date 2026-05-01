<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CustomerProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('satuan')) {
            $query->where('satuan', $request->satuan);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $units = Product::query()
            ->select('satuan')
            ->distinct()
            ->orderBy('satuan')
            ->pluck('satuan');

        return view('customer.product.index', compact('products', 'units'));
    }

    public function show(Product $product)
    {
        return view('customer.product.show', compact('product'));
    }
}
