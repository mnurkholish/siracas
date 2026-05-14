@php
    $navLinks = [
        [
            'nav' => 'Beranda',
            'route' => route('dashboard'),
        ],
        [
            'nav' => 'Produk',
            'route' => route('product.index'),
        ],
        [
            'nav' => 'Transaksi',
            'route' => route('transactions.index'),
        ],
        [
            'nav' => 'Review Saya',
            'route' => route('reviews.index'),
        ],
    ];

    $averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
@endphp

<x-layouts.public title="{{ $product->nama_produk }} - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-secondary-soft px-6 py-12 lg:px-10">
        <div class="mx-auto max-w-5xl rounded-lg border border-border bg-white p-6 shadow-sm">
            <a href="{{ route('product.index') }}"
                class="mb-6 inline-flex text-sm font-semibold text-primary-dark transition hover:text-text-body">
                Kembali ke produk
            </a>

            <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
                <img src="{{ $product->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp') }}"
                    alt="{{ $product->nama_produk }}"
                    class="aspect-square w-full rounded-lg border border-border-soft object-cover">

                <section>
                    <p class="text-xs font-bold uppercase tracking-[0.35em] text-muted-light">Detail Produk</p>
                    <h1 class="mt-3 text-3xl font-black text-muted-dark">{{ $product->nama_produk }}</h1>
                    <p class="mt-4 text-2xl font-black text-accent">
                        Rp{{ number_format((float) $product->harga, 0, ',', '.') }}
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-1 text-accent">
                            @for ($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $star <= round($averageRating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        <p class="text-sm font-bold text-muted-dark">
                            {{ number_format($averageRating, 1, ',', '.') }} / 5
                            <span class="font-semibold text-muted">({{ $product->reviews_count }} review)</span>
                        </p>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-secondary-soft px-4 py-3">
                            <p class="text-xs font-semibold uppercase text-muted-light">Stok</p>
                            <p class="mt-1 font-bold text-muted-dark">{{ $product->stok }}</p>
                        </div>
                        <div class="rounded-lg bg-secondary-soft px-4 py-3">
                            <p class="text-xs font-semibold uppercase text-muted-light">Satuan</p>
                            <p class="mt-1 font-bold uppercase text-muted-dark">{{ $product->satuan }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-base font-black text-muted-dark">Deskripsi</h2>
                        <p class="mt-2 text-sm leading-7 text-muted">
                            {{ $product->deskripsi ?: 'Deskripsi produk belum tersedia.' }}
                        </p>
                    </div>

                    @if ($errors->any())
                        <div
                            class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('cart.store', $product) }}" method="POST"
                        class="mt-8 rounded-lg border border-border-soft bg-surface p-4">
                        @csrf
                        <label for="quantity" class="text-sm font-bold text-muted-dark">Quantity</label>
                        <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                            <input type="number" id="quantity" name="quantity" min="1"
                                max="{{ $product->stok }}" value="{{ old('quantity', 1) }}"
                                @disabled($product->stok <= 0)
                                class="h-12 rounded-lg border border-border-strong bg-white px-4 text-sm font-bold text-muted-dark outline-none transition focus:border-primary focus:ring-2 focus:ring-border-soft sm:w-32">
                            <button type="submit" @disabled($product->stok <= 0)
                                class="inline-flex h-12 items-center justify-center rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary-dark disabled:cursor-not-allowed disabled:bg-surface-disabled">
                                {{ $product->stok > 0 ? 'Tambahkan ke Keranjang' : 'Stok Habis' }}
                            </button>
                            <button type="submit" formmethod="GET"
                                formaction="{{ route('products.buy-now.form', $product) }}"
                                @disabled($product->stok <= 0)
                                class="inline-flex h-12 items-center justify-center rounded-lg border border-border-strong bg-white px-5 text-sm font-bold text-text-body transition hover:bg-primary-soft disabled:cursor-not-allowed disabled:bg-surface-disabled disabled:text-muted">
                                Beli Sekarang
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <section class="mt-10 border-t border-border-soft pt-8">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-muted-light">Review Produk</p>
                        <h2 class="mt-2 text-2xl font-black text-muted-dark">Ulasan Customer</h2>
                    </div>
                    <p class="text-sm font-bold text-muted-dark">
                        Rata-rata {{ number_format($averageRating, 1, ',', '.') }} dari {{ $product->reviews_count }} review
                    </p>
                </div>

                @if ($product->reviews->isEmpty())
                    <div class="mt-6 rounded-lg border border-dashed border-border-strong bg-surface px-5 py-8 text-center">
                        <p class="font-bold text-muted-dark">Belum ada review untuk produk ini.</p>
                    </div>
                @else
                    <div class="mt-6 grid gap-4">
                        @foreach ($product->reviews as $review)
                            <article class="rounded-lg border border-border-soft bg-surface p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-3">
                                            <p class="font-black text-muted-dark">{{ $review->user?->username ?? 'Customer' }}</p>
                                            <div class="flex items-center gap-1 text-accent">
                                                @for ($star = 1; $star <= 5; $star++)
                                                    <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs font-semibold text-muted">
                                            {{ $review->created_at->format('d M Y') }}
                                        </p>
                                        <p class="mt-3 text-sm leading-7 text-muted-dark">{{ $review->isi }}</p>
                                        <x-review.admin-reply :review="$review" />
                                    </div>

                                    @if ($review->foto)
                                        <img src="{{ asset('storage/' . $review->foto) }}"
                                            alt="Foto review {{ $product->nama_produk }}"
                                            class="h-24 w-24 rounded-lg border border-border-soft object-cover">
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($product->reviews_count > $product->reviews->count())
                        <div class="mt-6 flex justify-center">
                            <x-button :href="route('product.reviews', $product)" variant="secondary" size="lg">
                                Lihat Semua Review
                            </x-button>
                        </div>
                    @endif
                @endif
            </section>
        </div>
    </main>

    <x-home.footer />
</x-layouts.public>
