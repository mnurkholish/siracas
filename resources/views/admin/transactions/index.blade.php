<x-layouts.admin :title="$title ?? 'Transaksi'" :subtitle="$subtitle ?? 'Kelola semua pesanan customer SIRACAS.'">
    <x-slot:actions>
        <form method="GET" action="{{ url()->current() }}" class="relative w-full sm:w-[430px]">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if (request('tanggal'))
                <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
            @endif

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari username atau produk"
                class="h-12 w-full rounded-full border-0 bg-white px-6 pr-14 text-sm text-gray-700 shadow-sm outline-none placeholder:text-gray-500 focus:ring-2 focus:ring-border-strong sm:px-8 sm:text-base">
            <button type="submit"
                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-primary-dark"
                aria-label="Cari transaksi">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                </svg>
            </button>
        </form>
    </x-slot:actions>

    <section class="table-wrap">
        <div class="flex flex-col gap-4 border-b border-gray-100 bg-white px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap gap-2">
                @php
                    $baseFilter = array_filter([
                        'search' => request('search'),
                        'tanggal' => request('tanggal'),
                    ]);
                    $allActive = blank(request('status'));
                    $allUrl = $baseFilter === []
                        ? url()->current()
                        : url()->current() . '?' . http_build_query($baseFilter);
                @endphp

                <a href="{{ $allUrl }}"
                    class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold transition {{ $allActive ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-soft hover:text-primary-dark' }}">
                    All
                </a>

                @foreach ($statusOptions ?? [] as $value => $label)
                    @php
                        $active = request('status') === $value;
                        $query = array_filter([
                            'search' => request('search'),
                            'tanggal' => request('tanggal'),
                            'status' => $value,
                        ]);
                    @endphp

                    <a href="{{ url()->current() . '?' . http_build_query($query) }}"
                        class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold transition {{ $active ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-soft hover:text-primary-dark' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <input name="tanggal" type="date" value="{{ request('tanggal') }}"
                    class="form-control input-control sm:w-44">
                <x-button type="submit" variant="secondary" size="lg">
                    Filter
                </x-button>
                <a href="{{ $resetRoute ?? url()->current() }}"
                    class="inline-flex h-11 items-center justify-center rounded-lg border border-border-strong bg-white px-5 text-sm font-semibold text-muted-dark transition hover:bg-primary-soft">
                    Reset
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-[760px]">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td class="font-semibold text-muted-dark">
                                {{ $transaction->user?->username ?? '-' }}
                            </td>
                            <td class="text-gray-600">{{ $transaction->tanggal->format('d M Y H:i:s') }}</td>
                            <td>
                                <x-badge :status="$transaction->status" />
                            </td>
                            <td class="text-right">
                                <x-button :href="route('admin.transactions.show', $transaction)" size="sm">
                                    Detail
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-500">
                                {{ $emptyMessage ?? 'Belum ada transaksi.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </section>
</x-layouts.admin>
