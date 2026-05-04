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

    <main class="page">
        <section class="mx-auto max-w-6xl">
            <div>
                <p class="eyebrow">Checkout</p>
                <h1 class="page-title">Konfirmasi Pesanan</h1>
            </div>

            @if ($errors->any())
                <div class="alert-danger mt-6">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <div class="empty-state mt-8">
                    <h2 class="text-xl font-black text-text-body">Keranjang masih kosong</h2>
                    <x-button :href="route('customer.product.index')" size="lg" class="mt-5">
                        Pilih Produk
                    </x-button>
                </div>
            @else
                <form action="{{ route('checkout.process') }}" method="POST" class="checkout-form mt-8 grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
                    @csrf

                    <div class="card overflow-hidden">
                        <div class="border-b border-border-soft px-5 py-4">
                            <h2 class="font-black text-muted-dark">Produk</h2>
                        </div>
                        <div class="divide-y divide-border-soft">
                            @foreach ($cartItems as $item)
                                @php
                                    $product = $item->product;
                                    $itemTotal = $item->quantity * (float) ($product?->harga ?? 0);
                                @endphp
                                <article class="grid gap-4 px-5 py-5 sm:grid-cols-[1fr_auto] sm:items-center">
                                    <div class="flex gap-4">
                                        <img src="{{ $product?->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp') }}"
                                            alt="{{ $product?->nama_produk ?? 'Produk' }}"
                                            class="h-20 w-20 shrink-0 rounded-lg border border-border-soft object-cover">
                                        <div>
                                            <h3 class="font-black text-muted-dark">{{ $product?->nama_produk ?? 'Produk tidak tersedia' }}</h3>
                                            <p class="mt-1 text-sm font-semibold text-muted">Qty: {{ $item->quantity }}</p>
                                            <p class="mt-1 text-sm font-bold text-accent">
                                                Rp{{ number_format((float) ($product?->harga ?? 0), 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-lg font-black text-text-body">
                                        Rp{{ number_format($itemTotal, 0, ',', '.') }}
                                    </p>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <aside class="card p-5">
                        <label for="address_id" class="text-sm font-bold text-muted-dark">Alamat</label>
                        <a href="{{ route('addresses.create', ['return_to' => request()->getRequestUri()]) }}"
                            class="float-right text-sm font-bold text-primary-dark transition hover:text-text-body">
                            Buat alamat
                        </a>
                        <select id="address_id" name="address_id"
                            class="form-control input-control mt-2">
                            <option value="">Pilih alamat</option>
                            @foreach ($addresses as $address)
                                @php($quote = $addressOngkir[$address->id] ?? null)
                                <option value="{{ $address->id }}"
                                    data-ongkir="{{ $quote['harga'] ?? '' }}"
                                    data-ongkir-message="{{ $quote['pesan'] ?? '' }}"
                                    @selected(old('address_id') == $address->id)>
                                    {{ $address->fullAddress() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($addresses->isEmpty())
                            <p class="mt-2 text-xs font-semibold text-danger">
                                Anda belum punya alamat. Buat alamat terlebih dahulu.
                            </p>
                        @endif

                        <label for="catatan" class="mt-5 block text-sm font-bold text-muted-dark">Catatan</label>
                        <textarea id="catatan" name="catatan" rows="4"
                            class="form-control textarea-control mt-2"
                            placeholder="Opsional">{{ old('catatan') }}</textarea>

                        <div class="mt-5 space-y-3 border-t border-border-soft pt-5">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-muted">Total barang</span>
                                <span class="text-base font-black text-text-body">Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-semibold text-muted">Ongkir</span>
                                <span id="ongkir-text" class="text-right text-sm font-black text-text-body">Pilih alamat</span>
                            </div>
                            <div id="ongkir-message" class="hidden rounded-lg bg-yellow-50 px-3 py-2 text-xs font-bold text-yellow-700">
                                Ongkir akan dikonfirmasi admin
                            </div>
                            <div class="flex items-center justify-between border-t border-border-soft pt-3">
                                <span class="text-sm font-semibold text-muted">Total bayar</span>
                                <span id="total-bayar-text" class="text-xl font-black text-accent">-</span>
                            </div>
                        </div>

                        <x-button type="submit" size="xl" :block="true" class="mt-5">
                            Buat Pesanan
                        </x-button>
                    </aside>
                </form>
            @endif
        </section>
    </main>

    <script>
        const totalBarang = Number(@json($total));
        const formatRupiah = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value);
        const addressSelect = document.getElementById('address_id');
        const ongkirText = document.getElementById('ongkir-text');
        const ongkirMessage = document.getElementById('ongkir-message');
        const totalBayarText = document.getElementById('total-bayar-text');

        function updateOngkirPreview() {
            const selected = addressSelect?.selectedOptions?.[0];
            const ongkir = selected?.dataset?.ongkir;
            const message = selected?.dataset?.ongkirMessage;

            if (!selected || !selected.value) {
                ongkirText.textContent = 'Pilih alamat';
                totalBayarText.textContent = '-';
                ongkirMessage.classList.add('hidden');
                return;
            }

            if (!ongkir) {
                ongkirText.textContent = '-';
                totalBayarText.textContent = '-';
                ongkirMessage.textContent = message || 'Ongkir akan dikonfirmasi admin';
                ongkirMessage.classList.remove('hidden');
                return;
            }

            const ongkirValue = Number(ongkir);
            ongkirText.textContent = formatRupiah(ongkirValue);
            totalBayarText.textContent = formatRupiah(totalBarang + ongkirValue);
            ongkirMessage.classList.add('hidden');
        }

        if (addressSelect && ongkirText && ongkirMessage && totalBayarText) {
            addressSelect.addEventListener('change', updateOngkirPreview);
            updateOngkirPreview();
        }

        document.querySelectorAll('.checkout-form').forEach((form) => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah yakin?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, pesan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: themeColor('primary'),
                    cancelButtonColor: themeColor('danger'),
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
