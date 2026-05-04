<x-layouts.admin :title="$title ?? 'Transaksi'" :subtitle="$subtitle ?? 'Kelola semua pesanan customer SIRACAS.'">
    <section class="table-wrap">
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
