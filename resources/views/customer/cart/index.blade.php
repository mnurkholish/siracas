@php
    $navLinks = [
        [
            'nav' => 'Beranda',
            'route' => route('customer.dashboard'),
        ],
        [
            'nav' => 'Produk',
            'route' => route('customer.product.index'),
        ],
        [
            'nav' => 'Transaksi',
            'route' => route('transactions.index'),
        ],
    ];

    $subtotal = $cartItems->sum(fn($item) => $item->quantity * (float) $item->harga_saat_dimasukkan);
@endphp

<x-layouts.public title="Keranjang - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-secondary-soft px-6 py-10 lg:px-10">
        <section class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.35em] text-muted-light">Keranjang</p>
                    <h1 class="mt-2 text-3xl font-black text-text-body">Produk Pilihan Saya</h1>
                    <p class="mt-2 text-sm text-muted">Atur jumlah produk sebelum lanjut ke checkout.</p>
                </div>

                <a href="{{ route('customer.product.index') }}"
                    class="inline-flex h-11 items-center justify-center rounded-lg border border-border-strong bg-white px-5 text-sm font-semibold text-text-body transition hover:bg-primary-soft">
                    Tambah Produk
                </a>
            </div>

            @if ($errors->any())
                <div
                    class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($cartItems->isEmpty())
                <div
                    class="mt-8 rounded-lg border border-dashed border-border-strong bg-white px-6 py-16 text-center shadow-sm">
                    <h2 class="text-xl font-black text-text-body">Keranjang masih kosong</h2>
                    <p class="mt-2 text-sm text-muted">Pilih produk organik SIRACAS yang ingin Anda pesan.</p>
                    <a href="{{ route('customer.product.index') }}"
                        class="mt-6 inline-flex h-11 items-center justify-center rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary-dark">
                        Lihat Produk
                    </a>
                </div>
            @else
                <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px] lg:items-start">
                    <div class="overflow-hidden rounded-lg border border-border bg-white shadow-sm">
                        <div
                            class="hidden grid-cols-[1fr_210px_170px_80px] gap-4 border-b border-border-soft px-5 py-4 text-xs font-bold uppercase tracking-[0.22em] text-muted-light md:grid">
                            <span>Produk</span>
                            <span>Quantity</span>
                            <span>Total Harga</span>
                            <span class="text-right">Aksi</span>
                        </div>

                        <div class="divide-y divide-border-soft">
                            @foreach ($cartItems as $item)
                                @php
                                    $product = $item->product;
                                    $itemTotal = $item->quantity * (float) $item->harga_saat_dimasukkan;
                                @endphp

                                <article
                                    class="grid gap-4 px-5 py-5 md:grid-cols-[1fr_210px_170px_80px] md:items-center">
                                    <div class="flex gap-4">
                                        <img src="{{ $product->foto ? asset('storage/' . $product->foto) : asset('images/banners/banner-2.webp') }}"
                                            alt="{{ $product->nama_produk }}"
                                            class="h-20 w-20 shrink-0 rounded-lg border border-border-soft object-cover">
                                        <div class="min-w-0">
                                            <h2 class="line-clamp-2 text-base font-black text-muted-dark">
                                                {{ $product->nama_produk }}</h2>
                                            <p class="mt-1 text-sm font-bold text-accent">
                                                Rp{{ number_format((float) $item->harga_saat_dimasukkan, 0, ',', '.') }}
                                            </p>
                                            <p class="mt-1 text-xs font-semibold text-muted">Stok: {{ $product->stok }}
                                            </p>
                                        </div>
                                    </div>

                                    <form action="{{ route('cart.items.update', $item) }}" method="POST"
                                        class="auto-cart-quantity-form flex items-center gap-2"
                                        data-original-quantity="{{ $item->quantity }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                            class="cart-quantity-minus inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-border-strong bg-white text-lg font-black text-text-body transition hover:bg-primary-soft"
                                            aria-label="Kurangi quantity {{ $product->nama_produk }}">
                                            -
                                        </button>
                                        <input type="number" name="quantity" min="0" max="{{ $product->stok }}"
                                            value="{{ old('quantity', $item->quantity) }}"
                                            class="cart-quantity-input h-10 w-20 rounded-lg border border-border-strong bg-surface px-3 text-center text-sm font-bold text-muted-dark outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-border-soft">
                                        <button type="button"
                                            class="cart-quantity-plus inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-border-strong bg-white text-lg font-black text-text-body transition hover:bg-primary-soft"
                                            aria-label="Tambah quantity {{ $product->nama_produk }}">
                                            +
                                        </button>
                                    </form>

                                    <div>
                                        <p class="text-xs font-semibold uppercase text-muted-light md:hidden">Total
                                            Harga</p>
                                        <p class="text-lg font-black text-text-body">
                                            Rp{{ number_format($itemTotal, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <form action="{{ route('cart.items.destroy', $item) }}" method="POST"
                                        class="delete-cart-item-form md:text-right">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex h-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 text-sm font-bold text-red-600 transition hover:bg-red-100 md:px-3"
                                            aria-label="Hapus {{ $product->nama_produk }}">
                                            Hapus
                                        </button>
                                    </form>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <aside class="rounded-lg border border-border bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-muted-light">Ringkasan</p>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4 text-muted">
                                <span>Total item</span>
                                <span class="font-bold text-muted-dark">{{ $cartItems->sum('quantity') }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4 text-muted">
                                <span>Subtotal</span>
                                <span class="text-lg font-black text-accent">
                                    Rp{{ number_format($subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="mt-5 rounded-lg border border-dashed border-border-strong bg-surface px-4 py-4 text-sm leading-6 text-muted">
                            Pastikan quantity dan stok sudah sesuai sebelum melanjutkan transaksi.
                        </div>
                        <a href="{{ route('checkout.form') }}"
                            class="mt-4 inline-flex h-12 w-full items-center justify-center rounded-lg bg-primary px-5 text-sm font-bold text-white transition hover:bg-primary-dark">
                            Checkout
                        </a>
                    </aside>
                </div>
            @endif
        </section>
    </main>

    <script>
        document.querySelectorAll('.delete-cart-item-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah yakin akan menghapus produk ini dalam keranjang?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
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

        document.querySelectorAll('.auto-cart-quantity-form').forEach((form) => {
            const input = form.querySelector('.cart-quantity-input');
            const minusButton = form.querySelector('.cart-quantity-minus');
            const plusButton = form.querySelector('.cart-quantity-plus');
            const min = Number(input.min || 0);
            const max = Number(input.max || 0);
            let timeoutId;
            let isSubmitting = false;

            const clampQuantity = (value) => {
                const numericValue = Number.parseInt(value, 10);

                if (Number.isNaN(numericValue)) {
                    return min;
                }

                return Math.min(Math.max(numericValue, min), max);
            };

            const submitQuantity = () => {
                const quantity = clampQuantity(input.value);
                input.value = quantity;

                if (isSubmitting || String(quantity) === form.dataset.originalQuantity) {
                    return;
                }

                isSubmitting = true;
                input.readOnly = true;
                minusButton.disabled = true;
                plusButton.disabled = true;
                form.submit();
            };

            const scheduleSubmit = () => {
                window.clearTimeout(timeoutId);
                timeoutId = window.setTimeout(submitQuantity, 650);
            };

            minusButton.addEventListener('click', () => {
                input.value = clampQuantity(Number(input.value || 0) - 1);
                submitQuantity();
            });

            plusButton.addEventListener('click', () => {
                input.value = clampQuantity(Number(input.value || 0) + 1);
                submitQuantity();
            });

            input.addEventListener('input', scheduleSubmit);
            input.addEventListener('blur', submitQuantity);
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    submitQuantity();
                }
            });
        });
    </script>

    <x-home.footer />
</x-layouts.public>
