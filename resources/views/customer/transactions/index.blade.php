@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('customer.dashboard')],
        ['nav' => 'Produk', 'route' => route('customer.product.index')],
        ['nav' => 'Keranjang', 'route' => route('cart.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
    ];

@endphp

<x-layouts.public title="Transaksi - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="siracas-page">
        <section class="siracas-container">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="siracas-eyebrow">Transaksi</p>
                    <h1 class="siracas-page-title">Pesanan Saya</h1>
                </div>
                <x-ui.button :href="route('customer.product.index')" variant="secondary" size="lg">
                    Belanja Lagi
                </x-ui.button>
            </div>

            <div class="siracas-table-wrap mt-8">
                <div class="overflow-x-auto">
                    <table class="siracas-table min-w-[840px]">
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
                                    <td class="font-semibold text-[#5f4f45]">{{ $transaction->ringkasanProduk() }}</td>
                                    <td class="text-[#8f8178]">{{ $transaction->totalQuantity() }}</td>
                                    <td class="font-black text-[#b37323]">
                                        Rp{{ number_format($transaction->totalHarga(), 0, ',', '.') }}
                                    </td>
                                    <td class="text-[#8f8178]">{{ $transaction->tanggal->format('d M Y') }}</td>
                                    <td>
                                        <x-ui.badge :status="$transaction->status" />
                                    </td>
                                    <td class="text-right">
                                        <x-ui.button :href="route('transactions.show', $transaction)" size="sm">
                                            Detail
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-[#9a8b81]">
                                        Belum ada transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="border-t border-[#eadfd7] px-5 py-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </section>
    </main>

    <x-home.footer />
</x-layouts.public>
