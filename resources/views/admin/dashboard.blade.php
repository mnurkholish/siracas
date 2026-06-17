@php
    $summaryCards = [
        [
            'label' => 'Pendapatan Hari Ini',
            'value' => 'Rp' . number_format($summary['todayRevenue'], 0, ',', '.'),
            'icon' => 'bi-cash-stack',
            'tone' => 'bg-success-soft text-success',
        ],
        [
            'label' => 'Transaksi Baru Hari Ini',
            'value' => number_format($summary['newTransactionsToday'], 0, ',', '.'),
            'icon' => 'bi-receipt',
            'tone' => 'bg-info-soft text-info',
        ],
        [
            'label' => 'Pesanan Perlu Diproses',
            'value' => number_format($summary['ordersNeedProcess'], 0, ',', '.'),
            'icon' => 'bi-box-seam',
            'tone' => 'bg-primary-soft text-primary-dark',
        ],
        [
            'label' => 'Pengajuan Garansi',
            'value' => number_format($summary['warrantyRequests'], 0, ',', '.'),
            'icon' => 'bi-shield-check',
            'tone' => 'bg-warning-soft text-warning',
        ],
        [
            'label' => 'Stok Rendah',
            'value' => number_format($summary['lowStockCount'], 0, ',', '.'),
            'icon' => 'bi-exclamation-triangle',
            'tone' => 'bg-danger-soft text-red-700',
        ],
    ];
@endphp

<x-layouts.admin title="Dashboard Admin" subtitle="Pantau aktivitas toko hari ini.">
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($summaryCards as $card)
                <article class="rounded-lg border border-border bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase text-gray-500">{{ $card['label'] }}</p>
                            <p class="mt-4 break-words text-2xl font-black text-black">{{ $card['value'] }}</p>
                        </div>
                        <span
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $card['tone'] }}">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </span>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="rounded-lg border border-border bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-1">
                <h2 class="text-lg font-bold text-black">Penjualan 7 Hari Terakhir</h2>
                <p class="text-sm text-gray-500">Total pendapatan bersih.</p>
            </div>
            <div class="h-72">
                <canvas id="dashboardSalesChart"></canvas>
            </div>
        </section>

        <section class="grid min-w-0 max-w-full gap-6 xl:grid-cols-[1.4fr_0.9fr]">
            <div class="w-full min-w-0 max-w-full overflow-hidden rounded-lg border border-border bg-white shadow-sm">
                <div
                    class="flex flex-col gap-3 border-b border-border-soft px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-black">Transaksi Perlu Ditindaklanjuti</h2>
                        <p class="mt-1 text-sm text-gray-500">Tangani pesanan yang telah dibayar, diproses, atau
                            mengajukan garansi.</p>
                    </div>
                    <x-button :href="route('admin.transactions.index')" variant="secondary" size="sm">
                        Lihat Semua
                    </x-button>
                </div>

                <div class="grid max-w-full gap-3 p-3 sm:hidden">
                    @forelse ($followUpTransactions as $transaction)
                        <article class="max-w-full overflow-hidden rounded-lg border border-border-soft bg-white p-4 shadow-sm">
                            <div class="grid gap-3">
                                <div class="min-w-0 max-w-full">
                                    <p class="text-xs font-bold uppercase text-muted">Order ID</p>
                                    <p class="mt-1 break-words font-semibold text-muted-dark [overflow-wrap:anywhere]">
                                        {{ $transaction->order_id ?: '#' . $transaction->id }}
                                    </p>
                                </div>
                                <x-button :href="route('admin.transactions.show', $transaction)" size="sm" class="w-full">
                                    Detail
                                </x-button>
                            </div>

                            <div class="mt-4 grid gap-3 text-sm">
                                <div class="grid min-w-0 gap-1">
                                    <span class="font-bold text-muted">Customer</span>
                                    <span class="min-w-0 break-words text-black [overflow-wrap:anywhere]">
                                        {{ $transaction->user?->username ?? '-' }}
                                    </span>
                                </div>

                                <div class="grid min-w-0 gap-1">
                                    <span class="font-bold text-muted">Status</span>
                                    <span class="flex min-w-0 flex-wrap gap-2">
                                        <x-badge :status="$transaction->status" />
                                        @if ($transaction->warranty_status === 'diajukan')
                                            <x-badge status="diajukan">Garansi Diajukan</x-badge>
                                        @endif
                                    </span>
                                </div>

                                <div class="grid min-w-0 gap-1">
                                    <span class="font-bold text-muted">Total</span>
                                    <span class="min-w-0 break-words font-bold text-success">
                                        Rp{{ number_format($transaction->totalAkhir(), 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="grid min-w-0 gap-1">
                                    <span class="font-bold text-muted">Tanggal</span>
                                    <span class="min-w-0 break-words text-gray-600">
                                        {{ $transaction->tanggal->format('d M Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-border-strong bg-white px-4 py-8 text-center text-sm text-gray-500">
                            Belum ada data
                        </div>
                    @endforelse
                </div>

                <div class="hidden sm:block">
                    <table class="admin-table table-fixed [&_td]:px-4 [&_th]:px-4">
                        <thead>
                            <tr>
                                <th class="w-[24%]">Order ID</th>
                                <th class="w-[14%]">Customer</th>
                                <th class="w-[18%]">Status</th>
                                <th class="w-[14%]">Total</th>
                                <th class="w-[16%]">Tanggal</th>
                                <th class="w-[14%] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($followUpTransactions as $transaction)
                                <tr>
                                    <td data-label="Order ID" class="break-words font-semibold text-muted-dark [overflow-wrap:anywhere]">
                                        {{ $transaction->order_id ?: '#' . $transaction->id }}
                                    </td>
                                    <td data-label="Customer" class="break-words [overflow-wrap:anywhere]">{{ $transaction->user?->username ?? '-' }}</td>
                                    <td data-label="Status">
                                        <div class="flex flex-wrap gap-2">
                                            <x-badge :status="$transaction->status" />
                                            @if ($transaction->warranty_status === 'diajukan')
                                                <x-badge status="diajukan">Garansi Diajukan</x-badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Total" class="font-bold text-success">
                                        Rp{{ number_format($transaction->totalAkhir(), 0, ',', '.') }}</td>
                                    <td data-label="Tanggal" class="break-words text-gray-600">{{ $transaction->tanggal->format('d M Y H:i') }}</td>
                                    <td data-label="Aksi" class="text-right">
                                        <x-button :href="route('admin.transactions.show', $transaction)" size="sm">
                                            Detail
                                        </x-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-500">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="w-full min-w-0 max-w-full overflow-hidden rounded-lg border border-border bg-white shadow-sm">
                <div
                    class="flex flex-col gap-3 border-b border-border-soft px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-black">Produk Stok Rendah</h2>
                        <p class="mt-1 text-sm text-gray-500">Stok produk menipis.</p>
                    </div>
                    <x-button :href="route('admin.products.index')" variant="secondary" size="sm">
                        Lihat Semua
                    </x-button>
                </div>

                <div class="max-w-full divide-y divide-border-soft">
                    @forelse ($lowStockProducts as $product)
                        <div class="grid min-w-0 max-w-full gap-3 px-5 py-4 sm:flex sm:items-center sm:justify-between sm:gap-4">
                            <div class="min-w-0 max-w-full">
                                <p class="whitespace-normal break-words font-semibold text-black [overflow-wrap:anywhere]">{{ $product->nama_produk }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ \App\Models\Product::SATUAN[$product->satuan] ?? ($product->satuan ?? '-') }}
                                </p>
                            </div>
                            <span class="w-fit rounded-full bg-danger-soft px-3 py-1 text-xs font-black text-red-700 sm:shrink-0">
                                {{ number_format($product->stok, 0, ',', '.') }} stok
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-gray-500">Belum ada data</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-border bg-white shadow-sm">
            <div class="border-b border-border-soft px-5 py-5">
                <h2 class="text-lg font-bold text-black">Transaksi Terbaru</h2>
                <p class="mt-1 text-sm text-gray-500">Lima transaksi terakhir di toko.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table min-w-[760px]">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestTransactions as $transaction)
                            <tr>
                                <td data-label="Order ID" class="font-semibold text-muted-dark">
                                    {{ $transaction->order_id ?: '#' . $transaction->id }}</td>
                                <td data-label="Customer">{{ $transaction->user?->username ?? '-' }}</td>
                                <td data-label="Status"><x-badge :status="$transaction->status" /></td>
                                <td data-label="Total" class="font-bold text-success">
                                    Rp{{ number_format($transaction->totalAkhir(), 0, ',', '.') }}</td>
                                <td data-label="Tanggal" class="text-gray-600">{{ $transaction->tanggal->format('d M Y H:i') }}</td>
                                <td data-label="Aksi" class="text-right">
                                    <x-button :href="route('admin.transactions.show', $transaction)" size="sm">
                                        Detail
                                    </x-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-500">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const salesChart = @json($salesChart);
            const money = (value) => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(value || 0);
            const color = (name, fallback) => window.themeColor?.(name) || fallback;

            new Chart(document.getElementById('dashboardSalesChart'), {
                type: 'line',
                data: {
                    labels: salesChart.labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: salesChart.revenue,
                        borderColor: color('success', '#166534'),
                        backgroundColor: 'rgba(22, 101, 52, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => money(context.parsed.y)
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#6d5b50'
                            },
                            grid: {
                                color: '#f3eee9'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#6d5b50',
                                callback: money
                            },
                            grid: {
                                color: '#f3eee9'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.admin>
