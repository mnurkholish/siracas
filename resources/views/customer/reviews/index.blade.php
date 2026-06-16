@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('dashboard')],
        ['nav' => 'Produk', 'route' => route('products.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
        ['nav' => 'Review Saya', 'route' => route('reviews.index')],
    ];
@endphp

<x-layouts.public title="Riwayat Penilaian - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="page-container">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Review Saya</p>
                    <h1 class="page-title">Riwayat Penilaian</h1>
                </div>
                <x-button :href="route('reviews.create')" variant="secondary" size="lg">
                    Beri Penilaian
                </x-button>
            </div>

            @if ($reviews->isEmpty())
                <div class="empty-state mt-8">
                    <p class="text-lg font-black text-muted-dark">Belum ada review.</p>
                    <p class="mt-2 text-sm text-muted">Review yang sudah kamu buat akan tampil di sini.</p>
                </div>
            @else
                <div class="mt-8 grid gap-5">
                    @foreach ($reviews as $review)
                        @php
                            $canEdit = $review->created_at->gte(now()->subDays(7));
                        @endphp
                        <article class="card p-5">
                            <div class="grid gap-5 lg:grid-cols-[120px_1fr_auto] lg:items-start">
                                <img src="{{ $review->product?->foto ? asset('storage/' . $review->product->foto) : asset('images/banners/banner-2.webp') }}"
                                    alt="{{ $review->product?->nama_produk ?? 'Produk' }}"
                                    class="h-28 w-28 rounded-lg border border-border-soft object-cover">

                                <div>
                                    <h2 class="font-black text-muted-dark">
                                        {{ $review->product?->nama_produk ?? 'Produk tidak tersedia' }}
                                    </h2>
                                    <div class="mt-2 flex items-center gap-1 text-accent">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <i
                                                class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="mt-3 text-sm leading-7 text-muted-dark">{{ $review->isi }}</p>
                                    <p class="mt-3 text-xs font-semibold text-muted">
                                        {{ $review->created_at->format('d M Y H:i') }}
                                    </p>
                                    @if ($canEdit)
                                        <div class="mt-4">
                                            <x-button :href="route('reviews.edit', $review)" variant="secondary" size="sm">
                                                Edit Review
                                            </x-button>
                                        </div>
                                    @endif
                                </div>

                                @if ($review->foto)
                                    <img src="{{ asset('storage/' . $review->foto) }}"
                                        alt="Foto review {{ $review->product?->nama_produk ?? 'produk' }}"
                                        class="h-28 w-28 rounded-lg border border-border-soft object-cover">
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($reviews->hasPages())
                    <div class="mt-6">
                        <x-pagination :paginator="$reviews" />
                    </div>
                @endif
            @endif
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
