@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('dashboard')],
        ['nav' => 'Produk', 'route' => route('product.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
        ['nav' => 'Review Saya', 'route' => route('reviews.index')],
    ];
@endphp

<x-layouts.public title="Review - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="page-container">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Review</p>
                    <h1 class="page-title">Produk yang Bisa Dinilai</h1>
                    <p class="mt-2 text-sm text-muted">Produk hanya muncul jika transaksi selesai, masih dalam 20 hari, dan belum pernah dinilai.</p>
                </div>
                <x-button :href="route('reviews.index')" variant="secondary" size="lg">
                    Review Saya
                </x-button>
            </div>

            @if ($errors->any())
                <div class="alert-danger mt-6">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($details->isEmpty())
                <div class="empty-state mt-8">
                    <p class="text-lg font-black text-muted-dark">Tidak ada produk yang bisa dinilai.</p>
                    <p class="mt-2 text-sm text-muted">Produk yang sudah dinilai atau melewati masa 20 hari tidak ditampilkan.</p>
                </div>
            @else
                <div class="table-wrap mt-8">
                    <div class="overflow-x-auto">
                        <table class="data-table min-w-[760px]">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Transaksi</th>
                                    <th>Tanggal Selesai</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details as $detail)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-4">
                                                <img src="{{ $detail->product?->foto ? asset('storage/' . $detail->product->foto) : asset('images/banners/banner-2.webp') }}"
                                                    alt="{{ $detail->product?->nama_produk ?? 'Produk' }}"
                                                    class="h-16 w-16 rounded-lg border border-border-soft object-cover">
                                                <span class="font-bold text-muted-dark">
                                                    {{ $detail->product?->nama_produk ?? 'Produk tidak tersedia' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="font-semibold text-muted-dark">#{{ $detail->transaction_id }}</td>
                                        <td class="text-muted">
                                            {{ $detail->transaction?->completed_at?->format('d M Y') }}
                                        </td>
                                        <td class="text-right">
                                            <x-button :href="route('review.show', $detail)" size="sm">
                                                Nilai
                                            </x-button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
