<section class="rounded-lg border border-border bg-white shadow-sm">
    <div class="border-b border-border-soft px-5 py-5">
        <h2 class="text-lg font-bold text-black">Transaksi Berdasarkan Status</h2>
        <p class="mt-1 text-sm text-gray-500">Jumlah transaksi untuk semua status pada {{ $period['label'] }}.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="admin-table min-w-[520px]">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah Transaksi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($statusCounts as $status)
                    <tr>
                        <td data-label="Status">
                            <x-badge :status="$status['status']" />
                        </td>
                        <td data-label="Jumlah Transaksi" class="font-bold text-black">{{ number_format($status['count'], 0, ',', '.') }}</td>
                        <td data-label="Keterangan" class="text-gray-500">
                            {{ in_array($status['status'], $validStatuses, true) ? 'Masuk perhitungan pendapatan' : 'Tidak masuk perhitungan pendapatan' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-10 text-center text-gray-500">Belum ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
