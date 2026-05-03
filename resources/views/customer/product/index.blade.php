@php
    $navLinks = [
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
        ],
    ];
@endphp

<x-layouts.public title="Produk - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-[#f7f2ed]">
        <section class="relative overflow-hidden bg-white">
            <div class="absolute inset-0">
                <img src="{{ asset('images/banners/banner-2.webp') }}" alt="Produk SIRACAS"
                    class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-[#3f3129]/80 via-[#6f5a4c]/55 to-transparent"></div>
            </div>

            <div class="relative mx-auto flex min-h-[320px] max-w-7xl items-end px-6 pb-12 pt-20 lg:px-10">
                <div class="max-w-2xl text-white">
                    <p class="text-xs font-bold uppercase tracking-[0.42em] text-[#ead8c8]">Katalog Produk</p>
                    <h1 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">Produk Organik SIRACAS</h1>
                    <p class="mt-4 text-sm leading-7 text-white/85 sm:text-base">
                        Pilih produk cacing, pupuk, dan olahan organik sesuai kebutuhan budidaya, pertanian, maupun
                        pakan.
                    </p>
                </div>
            </div>
        </section>

        <section class="px-6 py-8 lg:px-10 lg:py-10">
            <div class="mx-auto max-w-7xl">
                <form action="{{ route('customer.product.index') }}" method="GET"
                    class="grid gap-3 rounded-lg border border-[#e2d6cc] bg-white p-4 shadow-sm md:grid-cols-[1fr_220px_auto] md:items-center">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari produk organik"
                            class="h-12 w-full rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 pr-11 text-sm text-[#5d5048] outline-none transition placeholder:text-[#a5978d] focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]">
                        <button type="submit"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8c725f] transition hover:text-[#6f5a4c]"
                            aria-label="Cari produk">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </button>
                    </div>

                    <select name="satuan"
                        class="h-12 rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 text-sm font-medium text-[#5d5048] outline-none transition focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]">
                        <option value="">Semua satuan</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit }}" @selected(request('satuan') === $unit)>{{ strtoupper($unit) }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="h-12 flex-1 rounded-lg bg-[#9e836f] px-5 text-sm font-bold text-white transition hover:bg-[#8a725f] md:flex-none">
                            Terapkan
                        </button>
                        @if (request()->filled('search') || request()->filled('satuan'))
                            <a href="{{ route('customer.product.index') }}"
                                class="inline-flex h-12 items-center justify-center rounded-lg border border-[#e1d5cb] bg-white px-4 text-sm font-semibold text-[#6f5a4c] transition hover:bg-[#f7f1eb]">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="mt-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-[#6f5a4c]">Daftar Produk</h2>
                        <p class="mt-1 text-sm text-[#9a8b81]">
                            Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari
                            {{ $products->total() }} produk.
                        </p>
                    </div>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse ($products as $product)
                        <article
                            class="group flex min-h-full flex-col overflow-hidden rounded-lg border border-[#dfd4c9] bg-white shadow-[0_16px_30px_rgba(145,117,97,0.08)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_38px_rgba(145,117,97,0.14)]">
                            <a href="{{ route('customer.product.show', $product) }}"
                                class="block overflow-hidden bg-[#eee6df]">
                                <img src="{{ $product->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp') }}"
                                    alt="{{ $product->nama_produk }}"
                                    class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                            </a>

                            <div class="flex flex-1 flex-col p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="line-clamp-2 text-base font-black text-[#5f4f45]">
                                        {{ $product->nama_produk }}</h3>
                                    <span
                                        class="shrink-0 rounded-full bg-[#f1e7df] px-3 py-1 text-[11px] font-bold uppercase text-[#8c725f]">
                                        {{ $product->satuan }}
                                    </span>
                                </div>

                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-[#9a8b81]">
                                    {{ $product->deskripsi ?: 'Produk SIRACAS berkualitas untuk kebutuhan organik Anda.' }}
                                </p>

                                <div class="mt-4 flex items-center gap-1 text-[12px] text-[#d39b3d]">
                                    @for ($i = 0; $i < 5; $i++)
                                        <span>★</span>
                                    @endfor
                                    <span class="ml-2 text-xs font-medium text-[#b4a69c]">Review segera hadir</span>
                                </div>

                                <div class="mt-auto pt-5">
                                    <div class="mb-4 flex items-end justify-between gap-3">
                                        <div>
                                            <p class="text-xs text-[#aa9d94]">Harga</p>
                                            <p class="text-xl font-black text-[#b37323]">
                                                Rp{{ number_format((float) $product->harga, 0, ',', '.') }}
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-xs text-[#aa9d94]">Stok</p>
                                            <p
                                                class="text-sm font-bold {{ $product->stok > 0 ? 'text-[#59815f]' : 'text-[#d94b4b]' }}">
                                                {{ $product->stok > 0 ? $product->stok . ' tersedia' : 'Habis' }}
                                            </p>
                                        </div>
                                    </div>

                                    <a href="{{ route('customer.product.show', $product) }}"
                                        class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-[#a6866d] px-4 text-sm font-bold text-white transition hover:bg-[#8f715b]">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div
                            class="rounded-lg border border-dashed border-[#d8c8bb] bg-white px-6 py-14 text-center sm:col-span-2 lg:col-span-3 xl:col-span-4">
                            <h3 class="text-lg font-black text-[#6f5a4c]">Produk belum ditemukan</h3>
                            <p class="mt-2 text-sm text-[#9a8b81]">
                                Coba ubah kata kunci atau filter satuan untuk melihat produk lainnya.
                            </p>
                        </div>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="mt-8 rounded-lg border border-[#e2d6cc] bg-white px-4 py-4 shadow-sm">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
