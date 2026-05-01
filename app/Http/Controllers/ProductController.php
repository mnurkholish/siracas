<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('satuan', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(8)->withQueryString();

        return view('admin.product.index', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedProduct($request);

        unset($data['foto']);

        if ($file = $request->file('foto')) {
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();

                $destination = storage_path('app/public/products');

                if (!is_dir($destination)) {
                    mkdir($destination, 0777, true);
                }

                $file->move($destination, $filename);

                $data['foto'] = 'products/' . $filename;
            }
        }

        Product::create($data);

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk baru berhasil ditambah');
    }

    public function show(Product $product)
    {
        return response()->json($this->productPayload($product));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validatedProduct($request);

        unset($data['foto']);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            if ($file && $file->isValid()) {
                $this->deleteOldProductPhoto($product->foto);

                $filename = time() . '_' . $file->getClientOriginalName();

                try {
                    $path = $file->storeAs('products', $filename, 'public');

                    if (!$path) {
                        throw new \Exception('Gagal menyimpan file');
                    }

                    $data['foto'] = $path;
                } catch (\Throwable $e) {
                    $destination = storage_path('app/public/products');

                    if (!is_dir($destination)) {
                        mkdir($destination, 0777, true);
                    }

                    $file->move($destination, $filename);

                    $data['foto'] = 'products/' . $filename;
                }
            }
        }

        $product->update($data);

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    private function deleteOldProductPhoto($path)
    {
        try {
            if (!empty($path) && is_string($path)) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal hapus foto produk lama: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    private function validatedProduct(Request $request): array
    {
        return $request->validate([
            'nama_produk' => ['required', 'string', 'max:64'],
            'harga' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stok' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', Rule::in(['kg', 'gram', 'pcs', 'paket', 'karung'])],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['sometimes', 'nullable', 'image', 'max:2048'],
        ], [
            'required' => 'Harap lengkapi semua data.',
            'foto.image' => 'Data yang anda masukkan tidak valid!',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
            'satuan.in' => 'Data yang anda masukkan tidak valid!',
        ]);
    }

    private function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'nama_produk' => $product->nama_produk,
            'harga' => (float) $product->harga,
            'harga_display' => 'Rp' . number_format((float) $product->harga, 0, ',', '.'),
            'stok' => $product->stok,
            'satuan' => $product->satuan,
            'deskripsi' => $product->deskripsi ?? '-',
            'foto_url' => $product->foto ? asset('storage/' . $product->foto) : asset('images/logo.png'),
        ];
    }
}
