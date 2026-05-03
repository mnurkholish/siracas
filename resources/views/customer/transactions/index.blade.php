@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('customer.dashboard')],
        ['nav' => 'Produk', 'route' => route('customer.product.index')],
        ['nav' => 'Keranjang', 'route' => route('cart.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
    ];

    $badge = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'paid' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'expired' => 'bg-gray-100 text-gray-700',
    ];
@endphp

<x-layouts.public title="Transaksi - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-[#f7f2ed] px-6 py-10 lg:px-10">
        <section class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.35em] text-[#b7a69a]">Transaksi</p>
                    <h1 class="mt-2 text-3xl font-black text-[#6f5a4c]">Pesanan Saya</h1>
                </div>
                <a href="{{ route('customer.product.index') }}"
                    class="inline-flex h-11 items-center justify-center rounded-lg border border-[#d8c9bc] bg-white px-5 text-sm font-semibold text-[#6f5a4c] transition hover:bg-[#f7f1eb]">
                    Belanja Lagi
                </a>
            </div>

            <div class="mt-8 overflow-hidden rounded-lg border border-[#e2d6cc] bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[840px] text-left text-sm">
                        <thead class="bg-[#e3d9d1] text-[#4f443d]">
                            <tr>
                                <th class="px-5 py-4 font-bold">Produk</th>
                                <th class="px-5 py-4 font-bold">Quantity</th>
                                <th class="px-5 py-4 font-bold">Total Harga</th>
                                <th class="px-5 py-4 font-bold">Tanggal</th>
                                <th class="px-5 py-4 font-bold">Status</th>
                                <th class="px-5 py-4 text-right font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eadfd7]">
                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-[#fbf8f5]">
                                    <td class="px-5 py-4 font-semibold text-[#5f4f45]">{{ $transaction->ringkasanProduk() }}</td>
                                    <td class="px-5 py-4 text-[#8f8178]">{{ $transaction->totalQuantity() }}</td>
                                    <td class="px-5 py-4 font-black text-[#b37323]">
                                        Rp{{ number_format($transaction->totalHarga(), 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 text-[#8f8178]">{{ $transaction->tanggal->format('d M Y') }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold capitalize {{ $badge[$transaction->status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('transactions.show', $transaction) }}"
                                            class="inline-flex h-9 items-center justify-center rounded-lg bg-[#9e836f] px-4 text-xs font-bold text-white transition hover:bg-[#8a725f]">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-[#9a8b81]">
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
