<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Jika belum login
        if (!Auth::check()) {
            return view('welcome', ['products' => $this->products()]);
        }

        $user = Auth::user();

        // 2. Jika Admin
        if ($user->role === 'admin') {
            // Kita bisa sekalian passing data untuk dashboard admin di sini
            return redirect()->route('admin.dashboard');
        }

        // 3. Jika Customer
        return $this->customerDashboard();
    }

    public function customerDashboard()
    {
        return view('customer.dashboard', [
            'products' => $this->products(),
            'reviews' => $this->reviews(),
            'navLinks' => [
                [
                    'nav' => 'Beranda',
                    'route' => route('customer.dashboard'),
                ],
                [
                    'nav' => 'Produk',
                    'route' => route('customer.product.index'),
                ],
                [
                    'nav' => 'Keranjang',
                    'route' => route('cart.index'),
                ],
                [
                    'nav' => 'Transaksi',
                    'route' => route('transactions.index'),
                ]
            ],
        ]);
    }

    private function products(): array
    {
        if (!Schema::hasTable('products')) {
            return $this->fallbackProducts();
        }

        $products = Product::latest()
            ->take(4)
            ->get()
            ->map(function (Product $product) {
                return [
                    'name' => $product->nama_produk,
                    'description' => $product->deskripsi ?: 'Produk SIRACAS berkualitas untuk kebutuhan organik Anda.',
                    'price' => 'Rp' . number_format((float) $product->harga, 0, ',', '.'),
                    'image' => $product->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp'),
                    'url' => route('customer.product.show', $product),
                ];
            })
            ->all();

        if ($products !== []) {
            return $products;
        }

        return $this->fallbackProducts();
    }

    private function fallbackProducts(): array
    {
        return [
            [
                'name' => 'Cacing Tanah Segar',
                'description' => 'Cacing tanah segar berkualitas untuk kebutuhan budidaya, pakan, dan pengolahan organik.',
                'price' => 'Rp4.000',
                'image' => asset('images/banners/banner-2.webp'),
                'url' => route('customer.product.index'),
            ],
            [
                'name' => 'Cacing Tanah Kering',
                'description' => 'Cacing tanah kering yang higienis, cocok untuk campuran pakan bernutrisi tinggi.',
                'price' => 'Rp4.000',
                'image' => asset('images/banners/banner-1.webp'),
                'url' => route('customer.product.index'),
            ],
            [
                'name' => 'Pupuk Kascing',
                'description' => 'Pupuk organik padat hasil olahan cacing untuk membantu tanah lebih subur dan gembur.',
                'price' => 'Rp10.000',
                'image' => asset('images/banners/banner-2.webp'),
                'url' => route('customer.product.index'),
            ],
            [
                'name' => 'Tepung Cacing',
                'description' => 'Tepung cacing bernutrisi tinggi untuk pakan ternak, ikan, dan unggas.',
                'price' => 'Rp10.000',
                'image' => asset('images/banners/banner-1.webp'),
                'url' => route('customer.product.index'),
            ],
        ];
    }

    private function reviews(): array
    {
        return [
            [
                'name' => 'Kholis',
                'initials' => 'Kh',
                'review' => 'Cacing segar saya terima besar-besar dan bagus kualitasnya. Kemasan juga aman dan tidak berbau berlebihan.',
            ],
            [
                'name' => 'Lian',
                'initials' => 'Li',
                'review' => 'Sudah beberapa kali order di sini dan selalu puas. Pelayanannya cepat, respon admin baik, dan produknya konsisten.',
            ],
            [
                'name' => 'Thalia',
                'initials' => 'Th',
                'review' => 'Pupuk kascing sangat membantu tanaman saya jadi lebih subur. Teksturnya halus dan mudah digunakan.',
            ],
            [
                'name' => 'Bilqis',
                'initials' => 'Bi',
                'review' => 'Tepung cacing berkualitas, cocok dipakai untuk usaha pakan saya. Tekstur halus dan stok sering tersedia.',
            ],
            [
                'name' => 'Budi',
                'initials' => 'Bu',
                'review' => 'Kascing sangat ringan dan membuat drainase media tanam jauh lebih baik. Proses pemesanan juga mudah dan tidak ribet.',
            ],
            [
                'name' => 'Sugeng',
                'initials' => 'Su',
                'review' => 'Cacing saya diterima masih dalam kondisi aktif dan segar. Sangat puas dengan kebersihan pengiriman.',
            ],
        ];
    }
}
