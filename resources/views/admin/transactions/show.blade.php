@php
    $badge = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'paid' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'expired' => 'bg-gray-100 text-gray-700',
    ];
@endphp

<x-layouts.admin title="Detail Transaksi #{{ $transaction->id }}" subtitle="Customer: {{ $transaction->user?->username ?? '-' }}">
    @if ($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_360px] lg:items-start">
        <section class="overflow-hidden rounded-md bg-white shadow-sm">
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

        <aside class="rounded-md bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <span class="text-sm font-semibold text-gray-500">Status</span>
                <span class="rounded-full px-3 py-1 text-xs font-bold capitalize {{ $badge[$transaction->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $transaction->status }}
                </span>
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

            <form action="{{ route('admin.transactions.status', $transaction) }}" method="POST" class="update-status-form mt-6">
                @csrf
                @method('PATCH')
                <label for="status" class="text-sm font-bold text-black">Ubah Status</label>
                <select id="status" name="status"
                    class="mt-2 h-12 w-full rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]">
                    @foreach (\App\Models\Transaction::STATUSES as $status)
                        <option value="{{ $status }}" @selected(old('status', $transaction->status) === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="mt-4 inline-flex h-11 w-full items-center justify-center rounded-lg bg-[#9e836f] px-5 text-sm font-bold text-white transition hover:bg-[#8a725f]">
                    Simpan Status
                </button>
            </form>
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
