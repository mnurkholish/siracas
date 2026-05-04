@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('customer.dashboard')],
        ['nav' => 'Produk', 'route' => route('customer.product.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
    ];

    $statusLabel = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'kedaluwarsa' => 'Kedaluwarsa',
    ][$transaction->status] ?? $transaction->status;
@endphp

<x-layouts.public title="Detail Transaksi - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="mx-auto max-w-6xl">
            @if ($errors->any())
                <div class="alert-danger mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <a href="{{ route('transactions.index') }}"
                        class="text-sm font-semibold text-primary-dark">Kembali</a>
                    <h1 class="mt-2 text-3xl font-black text-text-body">Detail Transaksi #{{ $transaction->id }}</h1>
                    <p class="mt-1 text-sm text-muted">{{ $transaction->tanggal->format('d M Y H:i:s') }}</p>
                </div>
                <x-badge :status="$transaction->status">
                    {{ $statusLabel }}
                </x-badge>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_340px] lg:items-start">
                <div class="card overflow-hidden">
                    <div class="border-b border-border-soft px-5 py-4 font-black text-muted-dark">Produk</div>
                    <div class="divide-y divide-border-soft">
                        @foreach ($transaction->transactionDetails as $detail)
                            <article class="grid gap-4 px-5 py-5 sm:grid-cols-[1fr_auto] sm:items-center">
                                <div class="flex gap-4">
                                    <img src="{{ $detail->product?->foto ? asset('storage/' . $detail->product->foto) : asset('images/banners/banner-2.webp') }}"
                                        alt="{{ $detail->product?->nama_produk ?? 'Produk' }}"
                                        class="h-20 w-20 rounded-lg border border-border-soft object-cover">
                                    <div>
                                        <h2 class="font-black text-muted-dark">
                                            {{ $detail->product?->nama_produk ?? 'Produk tidak tersedia' }}</h2>
                                        <p class="mt-1 text-sm text-muted">Qty: {{ $detail->quantity }}</p>
                                        <p class="mt-1 text-sm font-bold text-accent">
                                            Rp{{ number_format((float) $detail->harga_saat_transaksi, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-lg font-black text-text-body">
                                    Rp{{ number_format($detail->subtotal(), 0, ',', '.') }}
                                </p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside class="card p-5">
                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-muted-light">Customer</p>
                    <dl class="mt-2 space-y-2 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Username</dt>
                            <dd class="text-right font-bold text-muted-dark">{{ $transaction->user?->username ?? '-' }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Nomor HP</dt>
                            <dd class="text-right font-bold text-muted-dark">{{ $transaction->user?->nomor_hp ?? '-' }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-muted">Tanggal</dt>
                            <dd class="text-right font-bold text-muted-dark">
                                {{ $transaction->tanggal->format('d M Y H:i:s') }}</dd>
                        </div>
                    </dl>

                    @if ($transaction->status === 'dibayar' || $transaction->paid_at || $transaction->payment_type)
                        <div class="mt-5 border-t border-border-soft pt-5">
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-muted-light">Pembayaran</p>
                            <dl class="mt-2 space-y-2 text-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-muted">Order ID</dt>
                                    <dd class="text-right font-bold text-muted-dark">
                                        {{ $transaction->order_id ?? '-' }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-muted">Metode</dt>
                                    <dd class="text-right font-bold text-muted-dark">
                                        {{ $transaction->payment_type ? str_replace('_', ' ', ucfirst($transaction->payment_type)) : '-' }}
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-muted">Dibayar Pada</dt>
                                    <dd class="text-right font-bold text-muted-dark">
                                        {{ $transaction->paid_at?->format('d M Y H:i:s') ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif

                    <div class="mt-5 border-t border-border-soft pt-5">
                        <p class="text-xs font-bold uppercase tracking-[0.3em] text-muted-light">Alamat</p>
                        <p class="mt-2 text-sm leading-6 text-muted-dark">
                            {{ $transaction->address?->fullAddress() ?: '-' }}</p>
                    </div>

                    <p class="mt-5 text-xs font-bold uppercase tracking-[0.3em] text-muted-light">Catatan</p>
                    <p class="mt-2 text-sm leading-6 text-muted-dark">{{ $transaction->catatan ?: '-' }}</p>

                    <div class="mt-5 flex items-center justify-between border-t border-border-soft pt-5">
                        <span class="text-sm font-semibold text-muted">Total</span>
                        <span
                            class="text-xl font-black text-accent">Rp{{ number_format($transaction->totalHarga(), 0, ',', '.') }}</span>
                    </div>

                    @if ($transaction->status === 'dibayar')
                        <div class="mt-5 rounded-lg bg-blue-50 px-4 py-3 text-center text-sm font-bold text-blue-700">
                            Sudah Dibayar
                        </div>
                    @endif

                    @if ($transaction->status === 'menunggu_pembayaran')
                        <x-button type="button" id="pay-now-button" size="lg" :block="true" class="mt-5">
                            Bayar Sekarang
                        </x-button>

                        <form action="{{ route('transactions.cancel', $transaction) }}" method="POST"
                            class="cancel-transaction-form mt-5">
                            @csrf
                            @method('PATCH')
                            <x-button type="submit" variant="danger-soft" size="lg" :block="true">
                                Batalkan Transaksi
                            </x-button>
                        </form>
                    @endif
                </aside>
            </div>
        </section>
    </main>

    @if ($transaction->status === 'menunggu_pembayaran')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif

    <script>
        const payNowButton = document.getElementById('pay-now-button');

        if (payNowButton) {
            payNowButton.addEventListener('click', async () => {
                payNowButton.disabled = true;
                payNowButton.textContent = 'Memproses...';

                try {
                    const response = await fetch("{{ route('transactions.pay', $transaction) }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const message = data.message || Object.values(data.errors || {})?.[0]?.[0] ||
                            'Gagal memulai pembayaran.';
                        throw new Error(message);
                    }

                    if (!window.snap) {
                        throw new Error('Midtrans Snap belum siap.');
                    }

                    window.snap.pay(data.snap_token, {
                        onSuccess: () => window.location.reload(),
                        onPending: () => window.location.reload(),
                        onError: () => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran gagal diproses.',
                            });
                        },
                        onClose: () => {
                            payNowButton.disabled = false;
                            payNowButton.textContent = 'Bayar Sekarang';
                        },
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: error.message,
                    });

                    payNowButton.disabled = false;
                    payNowButton.textContent = 'Bayar Sekarang';
                }
            });
        }

        document.querySelectorAll('.cancel-transaction-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah yakin?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, batalkan',
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
