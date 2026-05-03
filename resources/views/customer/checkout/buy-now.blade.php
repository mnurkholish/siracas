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

    <main class="bg-[#f7f2ed] px-6 py-10 lg:px-10">
        <form action="{{ route('products.buy-now.process', $product) }}" method="POST"
            class="buy-now-form mx-auto grid max-w-6xl gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
            @csrf

            <section class="rounded-lg border border-[#e2d6cc] bg-white p-5 shadow-sm">
                <a href="{{ route('customer.product.show', $product) }}" class="text-sm font-semibold text-[#8c725f]">
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
                        class="h-36 w-36 rounded-lg border border-[#eadfd7] object-cover">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.35em] text-[#b7a69a]">Beli Sekarang</p>
                        <h1 class="mt-2 text-2xl font-black text-[#5f4f45]">{{ $product->nama_produk }}</h1>
                        <p class="mt-2 text-xl font-black text-[#b37323]">
                            Rp{{ number_format((float) $product->harga, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-sm font-semibold text-[#9a8b81]">Stok: {{ $product->stok }}</p>
                    </div>
                </div>

                <label for="quantity" class="mt-6 block text-sm font-bold text-[#5f4f45]">Quantity</label>
                <input type="number" id="quantity" name="quantity" min="1" max="{{ $product->stok }}"
                    value="{{ old('quantity', $quantity) }}"
                    class="mt-2 h-12 w-32 rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 text-sm font-bold text-[#5d5048] outline-none focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]">
            </section>

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
                    <span class="text-sm font-semibold text-[#8f8178]">Estimasi total</span>
                    <span class="text-xl font-black text-[#b37323]">Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <button type="submit"
                    class="mt-5 inline-flex h-12 w-full items-center justify-center rounded-lg bg-[#9e836f] px-5 text-sm font-bold text-white transition hover:bg-[#8a725f]">
                    Buat Pesanan
                </button>
            </aside>
        </form>
    </main>

    <script>
        document.querySelectorAll('.buy-now-form').forEach((form) => {
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
