@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('dashboard')],
        ['nav' => 'Produk', 'route' => route('product.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
        ['nav' => 'Review Saya', 'route' => route('reviews.index')],
    ];

@endphp

<x-layouts.public title="Transaksi - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="page-container">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Transaksi</p>
                    <h1 class="page-title">Pesanan Saya</h1>
                </div>
                <x-button :href="route('product.index')" variant="secondary" size="lg">
                    Belanja Lagi
                </x-button>
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
                                    <td class="font-semibold text-muted-dark">{{ $transaction->ringkasanProduk() }}</td>
                                    <td class="text-muted">{{ $transaction->totalQuantity() }}</td>
                                    <td class="font-black text-accent">
                                        Rp{{ number_format($transaction->totalHarga(), 0, ',', '.') }}
                                    </td>
                                    <td class="text-muted">{{ $transaction->tanggal->format('d M Y') }}</td>
                                    <td>
                                        <x-badge :status="$transaction->status" />
                                    </td>
                                    <td class="text-right">
                                        <div class="inline-flex flex-wrap justify-end gap-2">
                                            <x-button :href="route('transactions.show', $transaction)" size="sm">
                                                Detail
                                            </x-button>
                                            @if (
                                                $transaction->status === 'selesai' &&
                                                    $transaction->completed_at &&
                                                    $transaction->completed_at->gte(now()->subDays(20)) &&
                                                    $transaction->reviewable_details_count > 0)
                                                <x-button :href="route('review')" variant="secondary" size="sm">
                                                    Beri Penilaian
                                                </x-button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-muted">
                                        Belum ada transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="border-t border-border-soft px-5 py-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
