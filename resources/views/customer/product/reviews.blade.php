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

<x-layouts.public title="Review {{ $product->nama_produk }} - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="page-container">
            <a href="{{ route('product.show', $product) }}"
                class="inline-flex text-sm font-semibold text-primary-dark transition hover:text-text-body">
                Kembali ke detail produk
            </a>

            <div class="mt-6 rounded-lg border border-border bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 border-b border-border-soft pb-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="eyebrow">Review Produk</p>
                        <h1 class="page-title">{{ $product->nama_produk }}</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-sm font-bold text-muted-dark">
                        <div class="flex items-center gap-1 text-accent">
                            @for ($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $star <= round($averageRating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                        <span>
                            {{ number_format($averageRating, 1, ',', '.') }} / 5 dari {{ $product->reviews_count }} review
                        </span>
                    </div>
                </div>

                @if ($reviews->isEmpty())
                    <div class="mt-6 rounded-lg border border-dashed border-border-strong bg-surface px-5 py-8 text-center">
                        <p class="font-bold text-muted-dark">Belum ada review untuk produk ini.</p>
                    </div>
                @else
                    <div class="mt-6 grid gap-4">
                        @foreach ($reviews as $review)
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

                    @if ($reviews->hasPages())
                        <div class="mt-6 border-t border-border-soft pt-5">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
