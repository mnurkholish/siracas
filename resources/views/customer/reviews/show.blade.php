@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('dashboard')],
        ['nav' => 'Produk', 'route' => route('product.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
        ['nav' => 'Review Saya', 'route' => route('reviews.index')],
    ];
@endphp

<x-layouts.public title="Nilai Produk - SIRACAS">
    <style>
        .star-rating {
            display: inline-flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 0.25rem;
        }

        .star-rating input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .star-rating label {
            cursor: pointer;
            color: #d8c9bc;
            font-size: 1.35rem;
            line-height: 1;
            transition: color 150ms ease;
        }

        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #b37323;
        }
    </style>

    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="mx-auto max-w-5xl">
            <a href="{{ route('review') }}"
                class="text-sm font-semibold text-primary-dark transition hover:text-text-body">
                Kembali
            </a>

            @if ($errors->any())
                <div class="alert-danger mt-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="card mt-6 p-5 sm:p-6">
                <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
                    <div>
                        <img src="{{ $detail->product?->foto ? asset('storage/' . $detail->product->foto) : asset('images/banners/banner-2.webp') }}"
                            alt="{{ $detail->product?->nama_produk ?? 'Produk' }}"
                            class="aspect-square w-full rounded-lg border border-border-soft object-cover">
                        <p class="mt-4 text-xs font-bold uppercase tracking-[0.25em] text-muted-light">
                            Transaksi #{{ $detail->transaction_id }}
                        </p>
                        <h1 class="mt-2 text-2xl font-black text-muted-dark">
                            {{ $detail->product?->nama_produk ?? 'Produk tidak tersedia' }}
                        </h1>
                        <p class="mt-1 text-sm text-muted">
                            Selesai {{ $detail->transaction?->completed_at?->format('d M Y') }}
                        </p>
                    </div>

                    <form action="{{ route('transactions.details.reviews.store', [$detail->transaction, $detail]) }}"
                        method="POST" enctype="multipart/form-data" class="grid gap-4">
                        @csrf

                        <div>
                            <label class="form-label">Rating</label>
                            <div class="star-rating" aria-label="Rating produk">
                                @for ($star = 5; $star >= 1; $star--)
                                    <input type="radio"
                                        id="rating-{{ $detail->id }}-{{ $star }}"
                                        name="rating"
                                        value="{{ $star }}"
                                        @checked((int) old('rating') === $star)>
                                    <label for="rating-{{ $detail->id }}-{{ $star }}" title="{{ $star }} bintang">
                                        <i class="bi bi-star-fill"></i>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <label for="isi-{{ $detail->id }}" class="form-label">Isi Penilaian</label>
                            <textarea id="isi-{{ $detail->id }}" name="isi" class="form-control textarea-control"
                                placeholder="Ceritakan pengalamanmu dengan produk ini.">{{ old('isi') }}</textarea>
                        </div>

                        <div>
                            <label for="foto-{{ $detail->id }}" class="form-label">Foto Penilaian</label>
                            <input type="file" id="foto-{{ $detail->id }}" name="foto"
                                accept="image/jpeg,image/png,image/webp"
                                class="form-control py-3">
                            <p class="mt-2 text-xs font-semibold text-muted">Opsional, JPG/PNG/WEBP maksimal 2 MB.</p>
                        </div>

                        <div>
                            <button type="submit"
                                class="inline-flex h-11 items-center justify-center rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary-dark">
                                Simpan Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
