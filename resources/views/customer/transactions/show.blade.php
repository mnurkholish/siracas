@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('dashboard')],
        ['nav' => 'Produk', 'route' => route('products.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
        ['nav' => 'Review Saya', 'route' => route('reviews.index')],
    ];

    $statusLabel = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'diterima' => 'Diterima',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'kedaluwarsa' => 'Kedaluwarsa',
    ][$transaction->status] ?? $transaction->status;

    $totalProduk = $transaction->totalHarga();
    $ongkir = (float) $transaction->ongkir;
    $canPay = $transaction->status === 'menunggu_pembayaran' && $ongkir > 0;
    $canComplete = $transaction->status === 'diterima' && $transaction->warranty_status !== 'diajukan';
    $canClaimWarranty = $transaction->canClaimWarranty();
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
                            @php
                                $review = $detail->reviews->first();
                            @endphp

                            <article class="px-5 py-5">
                                <div class="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-start">
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
                                </div>

                                @if ($transaction->status === 'selesai' && $detail->product)
                                    <div class="mt-5 rounded-lg border border-border-soft bg-surface p-4">
                                        @if ($review)
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <p class="text-sm font-black text-success">Sudah direview</p>
                                                    <div class="mt-2 flex items-center gap-1 text-accent">
                                                        @for ($star = 1; $star <= 5; $star++)
                                                            <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="mt-2 text-sm leading-6 text-muted-dark">{{ $review->isi }}</p>
                                                </div>
                                                @if ($review->foto)
                                                    <img src="{{ asset('storage/' . $review->foto) }}"
                                                        alt="Foto review {{ $detail->product->nama_produk }}"
                                                        class="h-20 w-20 rounded-lg border border-border-soft object-cover">
                                                @endif
                                            </div>
                                        @elseif ($transaction->completed_at && $transaction->completed_at->gte(now()->subDays(20)))
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-sm font-black text-muted-dark">Produk ini belum dinilai</p>
                                                    <p class="mt-1 text-sm text-muted">Berikan penilaian sebelum masa 20 hari berakhir.</p>
                                                </div>
                                                <x-button :href="route('reviews.show', $detail)" size="sm">
                                                    Beri Penilaian
                                                </x-button>
                                            </div>
                                        @else
                                            <p class="text-sm font-bold text-muted">Masa penilaian produk sudah berakhir.</p>
                                        @endif
                                    </div>
                                @endif
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

                    <div class="mt-5 border-t border-border-soft pt-5">
                        <dl class="space-y-2 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="font-semibold text-muted">Total Produk</dt>
                                <dd class="font-bold text-muted-dark">Rp{{ number_format($totalProduk, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="font-semibold text-muted">Ongkir</dt>
                                <dd class="font-bold text-muted-dark">Rp{{ number_format($ongkir, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4 border-t border-border-soft pt-3">
                                <dt class="font-black text-muted-dark">Total Akhir</dt>
                                <dd class="text-xl font-black text-accent">Rp{{ number_format($transaction->totalAkhir(), 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if ($transaction->warranty_status !== 'tidak_ada')
                        <div class="mt-5 border-t border-border-soft pt-5">
                            <p class="text-xs font-bold uppercase tracking-[0.3em] text-muted-light">Garansi</p>
                            <dl class="mt-2 space-y-2 text-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-muted">Status</dt>
                                    <dd><x-badge :status="$transaction->warranty_status" /></dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-muted">Diajukan Pada</dt>
                                    <dd class="text-right font-bold text-muted-dark">
                                        {{ $transaction->warranty_claimed_at?->format('d M Y H:i:s') ?? '-' }}
                                    </dd>
                                </div>
                                @if ($transaction->warranty_resolved_at)
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-muted">Diproses Pada</dt>
                                        <dd class="text-right font-bold text-muted-dark">
                                            {{ $transaction->warranty_resolved_at->format('d M Y H:i:s') }}
                                        </dd>
                                    </div>
                                @endif
                                @if ($transaction->refundAmount() > 0)
                                    <div class="flex items-start justify-between gap-4">
                                        <dt class="text-muted">Pengembalian</dt>
                                        <dd class="text-right font-bold text-danger">
                                            Rp{{ number_format($transaction->refundAmount(), 0, ',', '.') }}
                                        </dd>
                                    </div>
                                @endif
                                @if ($transaction->refund_note)
                                    <div>
                                        <dt class="text-muted">Catatan</dt>
                                        <dd class="mt-1 text-sm font-semibold leading-6 text-muted-dark">
                                            {{ $transaction->refund_note }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    @if ($transaction->status === 'dibayar')
                        <div class="mt-5 rounded-lg bg-blue-50 px-4 py-3 text-center text-sm font-bold text-blue-700">
                            Sudah Dibayar
                        </div>
                    @endif

                    @if ($transaction->status === 'dikirim')
                        <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold leading-6 text-amber-800">
                            Pesanan Anda sedang dalam pengiriman. Admin akan memperbarui status setelah pesanan diterima.
                        </div>
                    @endif

                    @if ($transaction->status === 'diterima')
                        <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold leading-6 text-amber-800">
                            Pesanan sudah diterima. Periksa kondisi produk terlebih dahulu. Jika produk sesuai, klik Selesaikan Transaksi. Jika ada kendala seperti cacing mati, rusak, jumlah tidak sesuai, atau produk tidak sesuai, ajukan garansi dan kirim bukti melalui WhatsApp.
                        </div>

                        @if ($transaction->warranty_status === 'diajukan')
                            <div class="mt-3 rounded-lg bg-gray-50 px-4 py-3 text-sm font-bold text-gray-600">
                                Garansi sedang diajukan. Lanjutkan chat admin melalui WhatsApp dan kirim foto/video bukti di sana.
                            </div>

                            @if ($warrantyWhatsappUrl)
                                <x-button :href="$warrantyWhatsappUrl" target="_blank" rel="noopener" variant="secondary" size="lg" :block="true" class="mt-3">
                                    Chat Garansi via WhatsApp
                                </x-button>
                            @else
                                <button type="button" disabled
                                    class="mt-3 inline-flex h-11 w-full cursor-not-allowed items-center justify-center rounded-lg border border-border-strong bg-gray-50 px-5 text-sm font-semibold text-gray-400">
                                    Nomor WhatsApp admin belum tersedia
                                </button>
                            @endif
                        @endif

                        @if ($canComplete)
                        <form action="{{ route('transactions.complete', $transaction) }}" method="POST"
                            class="complete-transaction-form mt-5">
                            @csrf
                            @method('PATCH')
                            <x-button type="submit" size="lg" :block="true">
                                Selesaikan Transaksi
                            </x-button>
                        </form>
                        @endif

                        @if ($canClaimWarranty)
                            <form action="{{ route('transactions.warranty', $transaction) }}" method="POST"
                                class="claim-warranty-form mt-3">
                                @csrf
                                @method('PATCH')
                                <x-button type="submit" variant="secondary" size="lg" :block="true">
                                    Ajukan Garansi
                                </x-button>
                            </form>
                        @elseif ($transaction->warranty_status === 'tidak_ada' && $transaction->received_at && $transaction->received_at->lt(now()->subDay()))
                            <div class="mt-3 rounded-lg bg-gray-50 px-4 py-3 text-sm font-bold text-gray-600">
                                Masa pengajuan garansi sudah berakhir.
                            </div>
                        @elseif (! $warrantyWhatsappUrl && $transaction->warranty_status === 'tidak_ada')
                            <button type="button" disabled
                                class="mt-3 inline-flex h-11 w-full cursor-not-allowed items-center justify-center rounded-lg border border-border-strong bg-gray-50 px-5 text-sm font-semibold text-gray-400">
                                Nomor WhatsApp admin belum tersedia
                            </button>
                        @endif

                        @if (in_array($transaction->warranty_status, ['diterima', 'ditolak'], true))
                            <div class="mt-3 rounded-lg bg-gray-50 px-4 py-3 text-sm font-bold text-gray-600">
                                Status garansi: {{ ucfirst($transaction->warranty_status) }}
                                @if ($transaction->warranty_resolved_at)
                                    ({{ $transaction->warranty_resolved_at->format('d M Y H:i:s') }})
                                @endif
                            </div>
                        @endif
                    @endif

                    @if ($transaction->status === 'menunggu_pembayaran')
                        @if ($canPay)
                            <x-button type="button" id="pay-now-button" size="lg" :block="true" class="mt-5">
                                Bayar Sekarang
                            </x-button>
                        @else
                            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
                                Ongkir belum ditentukan. Silakan chat admin terlebih dahulu.
                            </div>
                            <div class="mt-3 rounded-lg bg-gray-50 px-4 py-3 text-center text-sm font-bold text-gray-600">
                                Menunggu admin menentukan ongkir
                            </div>
                            @if ($adminWhatsappUrl)
                                <x-button :href="$adminWhatsappUrl" target="_blank" rel="noopener" variant="secondary" size="lg" :block="true" class="mt-3">
                                    Chat Admin
                                </x-button>
                            @endif
                        @endif

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

    @if ($canPay)
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

        document.querySelectorAll('.complete-transaction-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Konfirmasi pesanan selesai?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, selesai',
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

        document.querySelectorAll('.claim-warranty-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah Anda yakin ingin mengajukan garansi?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ajukan',
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
