<section class="grid gap-6 xl:grid-cols-[0.9fr_1.4fr]">
    <div class="rounded-lg border border-border bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-black">Statistik Customer</h2>
            <p class="mt-1 text-sm text-gray-500">Customer dihitung dari user dengan role customer.</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
            <div class="rounded-lg bg-primary-soft p-4">
                <p class="text-xs font-bold uppercase text-muted-dark">Total Customer</p>
                <p class="mt-2 text-2xl font-black text-primary-dark">{{ number_format($customerStats['total'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-info-soft p-4">
                <p class="text-xs font-bold uppercase text-info">Customer Baru Bulan Ini</p>
                <p class="mt-2 text-2xl font-black text-info">{{ number_format($customerStats['new'], 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg bg-success-soft p-4">
                <p class="text-xs font-bold uppercase text-success">Customer Aktif Bulan Ini</p>
                <p class="mt-2 text-2xl font-black text-success">{{ number_format($customerStats['active'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-border bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-black">Customer Dengan Total Pembelian Terbesar</h2>
            <p class="mt-1 text-sm text-gray-500">Top 5 berdasarkan pendapatan bersih transaksi valid.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-[560px]">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Transaksi</th>
                        <th>Total Pembelian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customerStats['topBuyers'] as $customer)
                        <tr>
                            <td data-label="Customer" class="font-semibold text-muted-dark">{{ $customer->name }}</td>
                            <td data-label="Transaksi">{{ number_format((int) $customer->transaction_count, 0, ',', '.') }}</td>
                            <td data-label="Total Pembelian" class="font-bold text-success">Rp{{ number_format((float) $customer->total_spent, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-gray-500">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
