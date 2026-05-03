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

    <main class="siracas-page">
        <section class="mx-auto max-w-6xl">
            <div>
                <p class="siracas-eyebrow">Checkout</p>
                <h1 class="siracas-page-title">Konfirmasi Pesanan</h1>
            </div>

            @if ($errors->any())
                <div class="siracas-alert-danger mt-6">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <div class="siracas-empty mt-8">
                    <h2 class="text-xl font-black text-[#6f5a4c]">Keranjang masih kosong</h2>
                    <x-ui.button :href="route('customer.product.index')" size="lg" class="mt-5">
                        Pilih Produk
                    </x-ui.button>
                </div>
            @else
                <form action="{{ route('checkout.process') }}" method="POST" class="checkout-form mt-8 grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
                    @csrf

                    <div class="siracas-card overflow-hidden">
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

                    <aside class="siracas-card p-5">
                        <label for="address_id" class="text-sm font-bold text-[#5f4f45]">Alamat</label>
                        <a href="{{ route('addresses.create', ['return_to' => request()->getRequestUri()]) }}"
                            class="float-right text-sm font-bold text-[#8c725f] transition hover:text-[#6f5a4c]">
                            Buat alamat
                        </a>
                        <select id="address_id" name="address_id"
                            class="siracas-input siracas-input-control mt-2">
                            <option value="">Pilih alamat</option>
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}" @selected(old('address_id') == $address->id)>
                                    {{ $address->fullAddress() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($addresses->isEmpty())
                            <p class="mt-2 text-xs font-semibold text-[#d94b4b]">
                                Anda belum punya alamat. Buat alamat terlebih dahulu.
                            </p>
                        @endif

                        <label for="catatan" class="mt-5 block text-sm font-bold text-[#5f4f45]">Catatan</label>
                        <textarea id="catatan" name="catatan" rows="4"
                            class="siracas-input siracas-textarea mt-2"
                            placeholder="Opsional">{{ old('catatan') }}</textarea>

                        <div class="mt-5 flex items-center justify-between border-t border-[#eadfd7] pt-5">
                            <span class="text-sm font-semibold text-[#8f8178]">Total</span>
                            <span class="text-xl font-black text-[#b37323]">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                        <x-ui.button type="submit" size="xl" :block="true" class="mt-5">
                            Buat Pesanan
                        </x-ui.button>
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
