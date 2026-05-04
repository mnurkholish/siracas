@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('customer.dashboard')],
        ['nav' => 'Produk', 'route' => route('customer.product.index')],
        ['nav' => 'Keranjang', 'route' => route('cart.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
    ];

    $total = $quantity * (float) $product->harga;
@endphp

<x-layouts.public title="Beli Sekarang - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-secondary-soft px-6 py-10 lg:px-10">
        <form action="{{ route('products.buy-now.process', $product) }}" method="POST"
            class="buy-now-form mx-auto grid max-w-6xl gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
            @csrf

            <section class="rounded-lg border border-border bg-white p-5 shadow-sm">
                <a href="{{ route('customer.product.show', $product) }}" class="text-sm font-semibold text-primary-dark">
                    Kembali ke produk
                </a>

                @if ($errors->any())
                    <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mt-6 flex flex-col gap-5 sm:flex-row">
                    <img src="{{ $product->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp') }}"
                        alt="{{ $product->nama_produk }}"
                        class="h-36 w-36 rounded-lg border border-border-soft object-cover">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-muted-light">Beli Sekarang</p>
                        <h1 class="mt-2 text-2xl font-black text-muted-dark">{{ $product->nama_produk }}</h1>
                        <p class="mt-2 text-xl font-black text-accent">
                            Rp{{ number_format((float) $product->harga, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-sm font-semibold text-muted">Stok: {{ $product->stok }}</p>
                    </div>
                </div>

                <label for="quantity" class="mt-6 block text-sm font-bold text-muted-dark">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" max="{{ $product->stok }}"
                    value="{{ old('quantity', $quantity) }}"
                    class="mt-2 h-12 w-32 rounded-lg border border-border-strong bg-surface px-4 text-sm font-bold text-muted-dark outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-border-soft">
            </section>

            <aside class="rounded-lg border border-border bg-white p-5 shadow-sm">
                <label for="address_id" class="text-sm font-bold text-muted-dark">Alamat</label>
                <a href="{{ route('addresses.create', ['return_to' => request()->getRequestUri()]) }}"
                    class="float-right text-sm font-bold text-primary-dark transition hover:text-text-body">
                    Buat alamat
                </a>
                <select id="address_id" name="address_id"
                    class="mt-2 h-12 w-full rounded-lg border border-border-strong bg-surface px-4 text-sm text-muted-dark outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-border-soft">
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
                    class="mt-2 w-full rounded-lg border border-border-strong bg-surface px-4 py-3 text-sm text-muted-dark outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-border-soft"
                    placeholder="Opsional">{{ old('catatan') }}</textarea>

                <div class="mt-5 space-y-3 border-t border-border-soft pt-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-muted">Total barang</span>
                        <span id="total-barang-text" class="text-base font-black text-text-body">Rp{{ number_format($total, 0, ',', '.') }}</span>
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

                <button type="submit"
                    class="mt-5 inline-flex h-12 w-full items-center justify-center rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary-dark">
                    Buat Pesanan
                </button>
            </aside>
        </form>
    </main>

    <script>
        const hargaProduk = Number(@json((float) $product->harga));
        const quantityInput = document.getElementById('quantity');
        const addressSelect = document.getElementById('address_id');
        const totalBarangText = document.getElementById('total-barang-text');
        const ongkirText = document.getElementById('ongkir-text');
        const ongkirMessage = document.getElementById('ongkir-message');
        const totalBayarText = document.getElementById('total-bayar-text');
        const formatRupiah = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value);

        function updateOngkirPreview() {
            const quantity = Math.max(Number(quantityInput?.value || 1), 1);
            const totalBarang = quantity * hargaProduk;
            const selected = addressSelect?.selectedOptions?.[0];
            const ongkir = selected?.dataset?.ongkir;
            const message = selected?.dataset?.ongkirMessage;

            totalBarangText.textContent = formatRupiah(totalBarang);

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

        if (addressSelect && quantityInput && totalBarangText && ongkirText && ongkirMessage && totalBayarText) {
            addressSelect.addEventListener('change', updateOngkirPreview);
            quantityInput.addEventListener('input', updateOngkirPreview);
            updateOngkirPreview();
        }

        document.querySelectorAll('.buy-now-form').forEach((form) => {
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
