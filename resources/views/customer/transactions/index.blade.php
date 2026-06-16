@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('dashboard')],
        ['nav' => 'Produk', 'route' => route('products.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
        ['nav' => 'Review Saya', 'route' => route('reviews.index')],
    ];

@endphp

<x-layouts.public :title="($title ?? 'Transaksi') . ' - SIRACAS'">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="page-container">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="eyebrow">{{ $eyebrow ?? 'Transaksi' }}</p>
                    <h1 class="page-title">{{ $title ?? 'Pesanan Saya' }}</h1>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <form action="{{ url()->current() }}" method="GET" class="relative w-full sm:w-[360px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ $searchPlaceholder ?? 'Cari produk' }}"
                            class="h-12 w-full rounded-full border border-border-soft bg-white px-6 pr-14 text-sm text-gray-700 shadow-sm outline-none placeholder:text-gray-500 focus:border-border-strong focus:ring-2 focus:ring-border-strong">
                        <button type="submit"
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-primary-dark"
                            aria-label="Cari transaksi">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                            </svg>
                        </button>
                    </form>

                    <div class="flex flex-wrap gap-2">
                        @if (request('search'))
                            <x-button :href="$resetRoute ?? url()->current()" variant="secondary" size="lg">
                                Reset
                            </x-button>
                        @endif
                        <x-button :href="$historyButtonRoute ?? route('transactions.history')" variant="secondary" size="lg">
                            {{ $historyButtonLabel ?? 'Riwayat Transaksi' }}
                        </x-button>
                        <x-button :href="route('products.index')" variant="secondary" size="lg">
                            Belanja Lagi
                        </x-button>
                    </div>
                </div>
            </div>

            <div class="table-wrap mt-8">
                <div class="overflow-x-auto">
                    <table class="data-table min-w-[840px]">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Quantity</th>
                                <th>Total Harga</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td data-label="Produk" class="font-semibold text-muted-dark">{{ $transaction->ringkasanProduk() }}</td>
                                    <td data-label="Quantity" class="text-muted">{{ $transaction->totalQuantity() }}</td>
                                    <td data-label="Total Harga" class="font-black text-accent">
                                        Rp{{ number_format($transaction->totalHarga(), 0, ',', '.') }}
                                    </td>
                                    <td data-label="Tanggal" class="text-muted">{{ $transaction->tanggal->format('d M Y') }}</td>
                                    <td data-label="Status">
                                        <x-badge :status="$transaction->status" />
                                    </td>
                                    <td data-label="Aksi" class="text-right">
                                        <div class="inline-flex flex-wrap justify-end gap-2">
                                            <x-button :href="route('transactions.show', $transaction)" size="sm">
                                                Detail
                                            </x-button>
                                            @if (
                                                $transaction->status === 'selesai' &&
                                                    $transaction->completed_at &&
                                                    $transaction->completed_at->gte(now()->subDays(20)) &&
                                                    $transaction->reviewable_details_count > 0)
                                                <x-button :href="route('reviews.create')" variant="secondary" size="sm">
                                                    Beri Penilaian
                                                </x-button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-muted">
                                        {{ $emptyMessage ?? 'Belum ada transaksi.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="border-t border-border-soft px-4 py-6">
                        <x-pagination :paginator="$transactions" />
                    </div>
                @endif
            </div>
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
