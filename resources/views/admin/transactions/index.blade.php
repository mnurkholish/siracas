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

<x-layouts.admin title="Transaksi" subtitle="Kelola semua pesanan customer SIRACAS.">
    <section class="overflow-hidden rounded-md bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm text-black">
                <thead>
                    <tr class="bg-[#e3d9d1]">
                        <th class="px-6 py-5 font-semibold">Customer</th>
                        <th class="px-6 py-5 font-semibold">Tanggal</th>
                        <th class="px-6 py-5 font-semibold">Status</th>
                        <th class="px-6 py-5 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($transactions as $transaction)
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-[#3f3935]">
                                {{ $transaction->user?->username ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $transaction->tanggal->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold capitalize {{ $badge[$transaction->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $transaction->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                    class="inline-flex h-9 items-center justify-center rounded-lg bg-[#9e836f] px-4 text-xs font-bold text-white transition hover:bg-[#8a725f]">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                Belum ada transaksi.
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
