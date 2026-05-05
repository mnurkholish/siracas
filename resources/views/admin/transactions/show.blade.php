@php
    $adminStatusOptions = match ($transaction->status) {
        'dibayar' => ['diproses'],
        'diproses' => ['dikirim'],
        'dikirim' => ['selesai'],
        default => [],
    };
    $statusLabels = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'kedaluwarsa' => 'Kedaluwarsa',
    ];
    $totalProduk = $transaction->totalHarga();
    $ongkir = (float) $transaction->ongkir;
    $canUpdateOngkir = $transaction->status === 'menunggu_pembayaran';
@endphp

<x-layouts.admin title="Detail Transaksi #{{ $transaction->id }}"
    subtitle="Customer: {{ $transaction->user?->username ?? '-' }}">
    @if ($errors->any())
        <div class="alert-danger mb-5">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
        <section class="card overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5">
                <h2 class="font-bold text-black">Produk</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($transaction->transactionDetails as $detail)
                    <article class="grid gap-4 px-6 py-5 sm:grid-cols-[1fr_auto] sm:items-center">
                        <div class="flex gap-4">
                            <img src="{{ $detail->product?->foto ? asset('storage/' . $detail->product->foto) : asset('images/logo.png') }}"
                                alt="{{ $detail->product?->nama_produk ?? 'Produk' }}"
                                class="h-16 w-16 rounded-md border border-gray-200 object-cover">
                            <div>
                                <h3 class="font-bold text-muted-dark">
                                    {{ $detail->product?->nama_produk ?? 'Produk tidak tersedia' }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Quantity: {{ $detail->quantity }}</p>
                                <p class="mt-1 text-sm font-semibold text-primary">
                                    Rp{{ number_format((float) $detail->harga_saat_transaksi, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <p class="font-bold text-black">Rp{{ number_format($detail->subtotal(), 0, ',', '.') }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="card p-6">
            <div class="flex items-center justify-between gap-4">
                <span class="text-sm font-semibold text-gray-500">Status</span>
                <x-badge :status="$transaction->status" />
            </div>

            <div class="mt-5 border-t border-gray-100 pt-5">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Customer</p>
                <dl class="mt-2 space-y-2 text-sm">
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-gray-500">Username</dt>
                        <dd class="text-right font-bold text-black">{{ $transaction->user?->username ?? '-' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-gray-500">Nomor HP</dt>
                        <dd class="text-right font-bold text-blue-500">
                            @if ($transaction->user?->nomor_hp)
                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', $transaction->user->nomor_hp) }}"
                                    target="_blank">
                                    {{ $transaction->user->nomor_hp }}
                                </a>
                            @else
                                {{ $transaction->user?->nomor_hp ?? '-' }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <dt class="text-gray-500">Tanggal</dt>
                        <dd class="text-right font-bold text-black">{{ $transaction->tanggal->format('d M Y H:i:s') }}
                        </dd>
                    </div>
                </dl>
            </div>

            @if ($transaction->status === 'dibayar' || $transaction->paid_at || $transaction->payment_type)
                <div class="mt-5 border-t border-gray-100 pt-5">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Pembayaran</p>
                    <dl class="mt-2 space-y-2 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500">Order ID</dt>
                            <dd class="text-right font-bold text-black">{{ $transaction->order_id ?? '-' }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500">Metode</dt>
                            <dd class="text-right font-bold text-black">
                                {{ $transaction->payment_type ? str_replace('_', ' ', ucfirst($transaction->payment_type)) : '-' }}
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <dt class="text-gray-500">Dibayar Pada</dt>
                            <dd class="text-right font-bold text-black">
                                {{ $transaction->paid_at?->format('d M Y H:i:s') ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            <div class="mt-5 border-t border-gray-100 pt-5">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Alamat</p>
                <p class="mt-2 text-sm leading-6 text-gray-700">{{ $transaction->address?->fullAddress() ?: '-' }}</p>
            </div>

            <div class="mt-5">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Catatan</p>
                <p class="mt-2 text-sm leading-6 text-gray-700">{{ $transaction->catatan ?: '-' }}</p>
            </div>

            <div class="mt-5 border-t border-gray-100 pt-5">
                <dl class="space-y-2 text-sm">
                    <div class="flex items-center justify-between gap-4">
                        <dt class="font-semibold text-gray-500">Total Produk</dt>
                        <dd class="font-bold text-black">Rp{{ number_format($totalProduk, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <dt class="font-semibold text-gray-500">Ongkir</dt>
                        <dd class="font-bold text-black">Rp{{ number_format($ongkir, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-t border-gray-100 pt-3">
                        <dt class="font-bold text-black">Total Akhir</dt>
                        <dd class="text-xl font-bold text-primary">Rp{{ number_format($transaction->totalAkhir(), 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            @if ($canUpdateOngkir)
                <form action="{{ route('admin.transactions.ongkir', $transaction) }}" method="POST"
                    class="update-ongkir-form mt-6">
                    @csrf
                    @method('PATCH')
                    <label for="ongkir" class="text-sm font-bold text-black">Ongkir</label>
                    <input type="number" id="ongkir" name="ongkir" min="0" step="100"
                        value="{{ old('ongkir', (int) $ongkir) }}" class="form-control input-control mt-2">
                    <p class="mt-2 text-xs font-semibold text-gray-500">
                        Ongkir hanya dapat diubah saat transaksi menunggu pembayaran.
                    </p>
                    <x-button type="submit" size="lg" :block="true" class="mt-4">
                        Simpan Ongkir
                    </x-button>
                </form>
            @endif

            @if ($adminStatusOptions !== [])
                <form action="{{ route('admin.transactions.status', $transaction) }}" method="POST"
                    class="update-status-form mt-6">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="text-sm font-bold text-black">Ubah Status</label>
                    <select id="status" name="status" class="form-control input-control mt-2">
                        @foreach ($adminStatusOptions as $status)
                            <option value="{{ $status }}" @selected(old('status') === $status)>
                                {{ $statusLabels[$status] ?? $status }}
                            </option>
                        @endforeach
                    </select>
                    <x-button type="submit" size="lg" :block="true" class="mt-4">
                        Simpan Status
                    </x-button>
                </form>
            @else
                <div class="mt-6 rounded-lg bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600">
                    @if ($transaction->status === 'menunggu_pembayaran')
                        Menunggu pembayaran customer
                    @else
                        Status sudah final
                    @endif
                </div>
            @endif
        </aside>
    </div>

    <script>
        document.querySelectorAll('.update-status-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah yakin?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, perbarui',
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

        document.querySelectorAll('.update-ongkir-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Simpan ongkir?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
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
</x-layouts.admin>
