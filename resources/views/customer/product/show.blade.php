@php
    $navLinks = [
        [
            'nav' => 'Beranda',
            'route' => route('customer.dashboard'),
        ],
        [
            'nav' => 'Tentang Kami',
            'route' => route('customer.dashboard') . '#tentang',
        ],
        [
            'nav' => 'Produk',
            'route' => route('customer.product.index'),
        ],
    ];
@endphp

<x-layouts.public title="{{ $product->nama_produk }} - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-[#f7f2ed] px-6 py-12 lg:px-10">
        <div class="mx-auto max-w-5xl rounded-lg border border-[#e2d6cc] bg-white p-6 shadow-sm">
            <a href="{{ route('customer.product.index') }}"
                class="mb-6 inline-flex text-sm font-semibold text-[#8c725f] transition hover:text-[#6f5a4c]">
                Kembali ke produk
            </a>

            <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
                <img src="{{ $product->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp') }}"
                    alt="{{ $product->nama_produk }}"
                    class="aspect-square w-full rounded-lg border border-[#eadfd7] object-cover">

                <section>
                    <p class="text-xs font-bold uppercase tracking-[0.35em] text-[#b7a69a]">Detail Produk</p>
                    <h1 class="mt-3 text-3xl font-black text-[#5f4f45]">{{ $product->nama_produk }}</h1>
                    <p class="mt-4 text-2xl font-black text-[#b37323]">
                        Rp{{ number_format((float) $product->harga, 0, ',', '.') }}
                    </p>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-[#f7f2ed] px-4 py-3">
                            <p class="text-xs font-semibold uppercase text-[#aa9d94]">Stok</p>
                            <p class="mt-1 font-bold text-[#5f4f45]">{{ $product->stok }}</p>
                        </div>
                        <div class="rounded-lg bg-[#f7f2ed] px-4 py-3">
                            <p class="text-xs font-semibold uppercase text-[#aa9d94]">Satuan</p>
                            <p class="mt-1 font-bold uppercase text-[#5f4f45]">{{ $product->satuan }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-base font-black text-[#5f4f45]">Deskripsi</h2>
                        <p class="mt-2 text-sm leading-7 text-[#8f8178]">
                            {{ $product->deskripsi ?: 'Deskripsi produk belum tersedia.' }}
                        </p>
                    </div>

                    <div class="mt-8 rounded-lg border border-dashed border-[#d8c8bb] bg-[#fbf8f5] px-4 py-4 text-sm text-[#8f8178]">
                        Keranjang, checkout, dan review akan disambungkan pada tahap berikutnya.
                    </div>
                </section>
            </div>
        </div>
    </main>

    <x-home.footer />
</x-layouts.public>
