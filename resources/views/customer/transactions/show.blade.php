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

<x-layouts.public title="Detail Transaksi - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-[#f7f2ed] px-6 py-10 lg:px-10">
        <section class="mx-auto max-w-6xl">
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <a href="{{ route('transactions.index') }}" class="text-sm font-semibold text-[#8c725f]">Kembali</a>
                    <h1 class="mt-2 text-3xl font-black text-[#6f5a4c]">Detail Transaksi #{{ $transaction->id }}</h1>
                    <p class="mt-1 text-sm text-[#9a8b81]">{{ $transaction->tanggal->format('d M Y') }}</p>
                </div>
                <span class="w-fit rounded-full px-3 py-1 text-xs font-bold capitalize {{ $badge[$transaction->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $transaction->status }}
                </span>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start">
                <div class="overflow-hidden rounded-lg border border-[#e2d6cc] bg-white shadow-sm">
                    <div class="border-b border-[#eadfd7] px-5 py-4 font-black text-[#5f4f45]">Produk</div>
                    <div class="divide-y divide-[#eadfd7]">
                        @foreach ($transaction->transactionDetails as $detail)
                            <article class="grid gap-4 px-5 py-5 sm:grid-cols-[1fr_auto] sm:items-center">
                                <div class="flex gap-4">
                                    <img src="{{ $detail->product?->foto ? asset('storage/' . $detail->product->foto) : asset('images/banners/banner-2.webp') }}"
                                        alt="{{ $detail->product?->nama_produk ?? 'Produk' }}"
                                        class="h-20 w-20 rounded-lg border border-[#eadfd7] object-cover">
                                    <div>
                                        <h2 class="font-black text-[#5f4f45]">{{ $detail->product?->nama_produk ?? 'Produk tidak tersedia' }}</h2>
                                        <p class="mt-1 text-sm text-[#8f8178]">Qty: {{ $detail->quantity }}</p>
                                        <p class="mt-1 text-sm font-bold text-[#b37323]">
                                            Rp{{ number_format((float) $detail->harga_saat_transaksi, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-lg font-black text-[#6f5a4c]">
                                    Rp{{ number_format($detail->subtotal(), 0, ',', '.') }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside class="rounded-lg border border-[#e2d6cc] bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-[#b7a69a]">Alamat</p>
                    <p class="mt-2 text-sm leading-6 text-[#5f4f45]">{{ $transaction->address?->fullAddress() ?: '-' }}</p>

                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.3em] text-[#b7a69a]">Catatan</p>
                    <p class="mt-2 text-sm leading-6 text-[#5f4f45]">{{ $transaction->catatan ?: '-' }}</p>

                    <div class="mt-5 flex items-center justify-between border-t border-[#eadfd7] pt-5">
                        <span class="text-sm font-semibold text-[#8f8178]">Total</span>
                        <span class="text-xl font-black text-[#b37323]">Rp{{ number_format($transaction->totalHarga(), 0, ',', '.') }}</span>
                    </div>

                    @if ($transaction->status === 'pending')
                        <form action="{{ route('transactions.cancel', $transaction) }}" method="POST" class="cancel-transaction-form mt-5">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="inline-flex h-11 w-full items-center justify-center rounded-lg border border-red-200 bg-red-50 px-5 text-sm font-bold text-red-600 transition hover:bg-red-100">
                                Batalkan Transaksi
                            </button>
                        </form>
                    @endif
                </aside>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('.cancel-transaction-form').forEach((form) => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah yakin?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, batalkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#9e836f',
                    cancelButtonColor: '#d94b4b',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    <x-home.footer />
</x-layouts.public>
