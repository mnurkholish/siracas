@php
    $adminStatusOptions = match ($transaction->status) {
        'paid' => ['processing', 'completed'],
        'processing' => ['completed'],
        default => [],
    };
@endphp

<x-layouts.admin title="Detail Transaksi #{{ $transaction->id }}" subtitle="Customer: {{ $transaction->user?->username ?? '-' }}">
    @if ($errors->any())
        <div class="siracas-alert-danger mb-5">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
        <section class="siracas-card overflow-hidden">
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
                                <h3 class="font-bold text-[#3f3935]">{{ $detail->product?->nama_produk ?? 'Produk tidak tersedia' }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Quantity: {{ $detail->quantity }}</p>
                                <p class="mt-1 text-sm font-semibold text-[#9e836f]">
                                    Rp{{ number_format((float) $detail->harga_saat_transaksi, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <p class="font-bold text-black">Rp{{ number_format($detail->subtotal(), 0, ',', '.') }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <aside class="siracas-card p-6">
            <div class="flex items-center justify-between gap-4">
                <span class="text-sm font-semibold text-gray-500">Status</span>
                <x-ui.badge :status="$transaction->status" />
            </div>

            <div class="mt-5 border-t border-gray-100 pt-5">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Alamat</p>
                <p class="mt-2 text-sm leading-6 text-gray-700">{{ $transaction->address?->fullAddress() ?: '-' }}</p>
            </div>

            <div class="mt-5">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-gray-400">Catatan</p>
                <p class="mt-2 text-sm leading-6 text-gray-700">{{ $transaction->catatan ?: '-' }}</p>
            </div>

            <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-5">
                <span class="text-sm font-semibold text-gray-500">Total Harga</span>
                <span class="text-xl font-bold text-[#9e836f]">Rp{{ number_format($transaction->totalHarga(), 0, ',', '.') }}</span>
            </div>

            @if ($adminStatusOptions !== [])
                <form action="{{ route('admin.transactions.status', $transaction) }}" method="POST" class="update-status-form mt-6">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="text-sm font-bold text-black">Ubah Status</label>
                    <select id="status" name="status"
                        class="siracas-input siracas-input-control mt-2">
                        @foreach ($adminStatusOptions as $status)
                            <option value="{{ $status }}" @selected(old('status') === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    <x-ui.button type="submit" size="lg" :block="true" class="mt-4">
                        Simpan Status
                    </x-ui.button>
                </form>
            @else
                <div class="mt-6 rounded-lg bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600">
                    @if ($transaction->status === 'pending')
                        Menunggu pembayaran customer
                    @else
                        Status transaksi sudah final
                    @endif
                </div>
            @endif
        </aside>
    </div>

    <script>
        document.querySelectorAll('.update-status-form').forEach((form) => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Apakah yakin?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, perbarui',
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
</x-layouts.admin>
