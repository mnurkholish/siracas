@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('customer.dashboard')],
        ['nav' => 'Produk', 'route' => route('customer.product.index')],
        ['nav' => 'Keranjang', 'route' => route('cart.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
    ];

    $total = $cartItems->sum(fn ($item) => $item->quantity * (float) ($item->product?->harga ?? 0));
@endphp

<x-layouts.public title="Checkout - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-[#f7f2ed] px-6 py-10 lg:px-10">
        <section class="mx-auto max-w-6xl">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-[#b7a69a]">Checkout</p>
                <h1 class="mt-2 text-3xl font-black text-[#6f5a4c]">Konfirmasi Pesanan</h1>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <div class="mt-8 rounded-lg border border-dashed border-[#d8c8bb] bg-white px-6 py-14 text-center shadow-sm">
                    <h2 class="text-xl font-black text-[#6f5a4c]">Keranjang masih kosong</h2>
                    <a href="{{ route('customer.product.index') }}"
                        class="mt-5 inline-flex h-11 items-center justify-center rounded-lg bg-[#9e836f] px-5 text-sm font-bold text-white transition hover:bg-[#8a725f]">
                        Pilih Produk
                    </a>
                </div>
            @else
                <form action="{{ route('checkout.process') }}" method="POST" class="checkout-form mt-8 grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
                    @csrf

                    <div class="overflow-hidden rounded-lg border border-[#e2d6cc] bg-white shadow-sm">
                        <div class="border-b border-[#eadfd7] px-5 py-4">
                            <h2 class="font-black text-[#5f4f45]">Produk</h2>
                        </div>
                        <div class="divide-y divide-[#eadfd7]">
                            @foreach ($cartItems as $item)
                                @php
                                    $product = $item->product;
                                    $itemTotal = $item->quantity * (float) ($product?->harga ?? 0);
                                @endphp
                                <article class="grid gap-4 px-5 py-5 sm:grid-cols-[1fr_auto] sm:items-center">
                                    <div class="flex gap-4">
                                        <img src="{{ $product?->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp') }}"
                                            alt="{{ $product?->nama_produk ?? 'Produk' }}"
                                            class="h-20 w-20 shrink-0 rounded-lg border border-[#eadfd7] object-cover">
                                        <div>
                                            <h3 class="font-black text-[#5f4f45]">{{ $product?->nama_produk ?? 'Produk tidak tersedia' }}</h3>
                                            <p class="mt-1 text-sm font-semibold text-[#9a8b81]">Qty: {{ $item->quantity }}</p>
                                            <p class="mt-1 text-sm font-bold text-[#b37323]">
                                                Rp{{ number_format((float) ($product?->harga ?? 0), 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-lg font-black text-[#6f5a4c]">
                                        Rp{{ number_format($itemTotal, 0, ',', '.') }}
                                    </p>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <aside class="rounded-lg border border-[#e2d6cc] bg-white p-5 shadow-sm">
                        <label for="address_id" class="text-sm font-bold text-[#5f4f45]">Alamat</label>
                        <select id="address_id" name="address_id"
                            class="mt-2 h-12 w-full rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 text-sm text-[#5d5048] outline-none focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]">
                            <option value="">Pilih alamat</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}" @selected(old('address_id') == $address->id)>
                                    {{ $address->fullAddress() }}
                                </option>
                            @endforeach
                        </select>

                        <label for="catatan" class="mt-5 block text-sm font-bold text-[#5f4f45]">Catatan</label>
                        <textarea id="catatan" name="catatan" rows="4"
                            class="mt-2 w-full rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 py-3 text-sm text-[#5d5048] outline-none focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]"
                            placeholder="Opsional">{{ old('catatan') }}</textarea>

                        <div class="mt-5 flex items-center justify-between border-t border-[#eadfd7] pt-5">
                            <span class="text-sm font-semibold text-[#8f8178]">Total</span>
                            <span class="text-xl font-black text-[#b37323]">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit"
                            class="mt-5 inline-flex h-12 w-full items-center justify-center rounded-lg bg-[#9e836f] px-5 text-sm font-bold text-white transition hover:bg-[#8a725f]">
                            Buat Pesanan
                        </button>
                    </aside>
                </form>
            @endif
        </section>
    </main>

    <script>
        document.querySelectorAll('.checkout-form').forEach((form) => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah yakin?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, pesan',
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
