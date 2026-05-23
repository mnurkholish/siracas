<section class="grid gap-6">
    <div class="rounded-lg border border-border bg-white p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-black">Penjualan Harian {{ $period['label'] }}</h2>
            <p class="mt-1 text-sm text-gray-500">Pendapatan bersih dan jumlah transaksi per hari.</p>
        </div>
        <div class="h-80">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-lg border border-border bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-black">Penjualan Bulanan {{ $period['year'] }}</h2>
                <p class="mt-1 text-sm text-gray-500">Performa pendapatan bersih sepanjang tahun.</p>
            </div>
            <div class="h-80">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>

        <div class="rounded-lg border border-border bg-white p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-black">Status Transaksi</h2>
                <p class="mt-1 text-sm text-gray-500">Distribusi transaksi pada periode ini.</p>
            </div>
            <div class="h-80">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-border bg-white p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-black">Produk Terlaris</h2>
            <p class="mt-1 text-sm text-gray-500">Top 5 produk berdasarkan jumlah terjual.</p>
        </div>
        <div class="h-80">
            <canvas id="bestSellingChart"></canvas>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chartData = @json($charts);
        const money = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value || 0);
        const color = (name, fallback) => window.themeColor?.(name) || fallback;

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        boxWidth: 12,
                        color: '#5f4f45'
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
                        color: '#6d5b50'
                    },
                    grid: {
                        color: '#f3eee9'
                    }
                }
            }
        };

        new Chart(document.getElementById('dailySalesChart'), {
            type: 'line',
            data: {
                labels: chartData.dailySales.labels,
                datasets: [{
                    label: 'Pendapatan Bersih',
                    data: chartData.dailySales.revenue,
                    borderColor: color('success', '#166534'),
                    backgroundColor: 'rgba(22, 101, 52, 0.12)',
                    fill: true,
                    tension: 0.35,
                    yAxisID: 'y'
                }, {
                    label: 'Transaksi',
                    data: chartData.dailySales.transactions,
                    borderColor: color('info', '#1d4ed8'),
                    backgroundColor: 'rgba(29, 78, 216, 0.12)',
                    tension: 0.35,
                    yAxisID: 'y1'
                }]
            },
            options: {
                ...commonOptions,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        ticks: {
                            callback: money
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('monthlySalesChart'), {
            type: 'bar',
            data: {
                labels: chartData.monthlySales.labels,
                datasets: [{
                    label: 'Pendapatan Bersih',
                    data: chartData.monthlySales.revenue,
                    backgroundColor: color('primary', '#9e836f')
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    ...commonOptions.scales,
                    y: {
                        ...commonOptions.scales.y,
                        ticks: {
                            callback: money
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: chartData.statusCounts.labels,
                datasets: [{
                    data: chartData.statusCounts.counts,
                    backgroundColor: ['#fef3c7', '#dbeafe', '#c7d2fe', '#f7f1eb', '#bfdbfe', '#dcfce7', '#fef2f2', '#e5e7eb']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        new Chart(document.getElementById('bestSellingChart'), {
            type: 'bar',
            data: {
                labels: chartData.bestSellingProducts.labels,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: chartData.bestSellingProducts.quantities,
                    backgroundColor: color('accent', '#b37323')
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y'
            }
        });
    });
</script>
