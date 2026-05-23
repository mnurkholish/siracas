@php
    $productSections = [
        ['title' => 'Produk Terlaris', 'items' => $productStats['bestSelling'], 'metric' => 'quantity_sold', 'suffix' => 'terjual'],
        ['title' => 'Produk Omzet Tertinggi', 'items' => $productStats['highestRevenue'], 'metric' => 'total_revenue', 'money' => true],
        ['title' => 'Produk Stok Rendah', 'items' => $productStats['lowStock'], 'metric' => 'stock', 'suffix' => 'stok'],
        ['title' => 'Produk Rating Rendah', 'items' => $productStats['lowRating'], 'metric' => 'average_rating', 'suffix' => 'rating'],
    ];
@endphp

<section class="rounded-lg border border-border bg-white p-5 shadow-sm">
    <div class="mb-5 flex flex-col gap-1">
        <h2 class="text-lg font-bold text-black">Statistik Produk</h2>
        <p class="text-sm text-gray-500">Top 5 produk untuk setiap kategori pada periode laporan.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        @foreach ($productSections as $section)
            <div class="rounded-lg border border-border-soft bg-surface p-4">
                <h3 class="mb-4 text-sm font-black uppercase text-muted-dark">{{ $section['title'] }}</h3>

                <div class="space-y-3">
                    @forelse ($section['items'] as $product)
                        @php
                            $metric = data_get($product, $section['metric'], 0);
                            $metricText = !empty($section['money'])
                                ? 'Rp' . number_format((float) $metric, 0, ',', '.')
                                : number_format((float) $metric, (string) $section['metric'] === 'average_rating' ? 1 : 0, ',', '.') . ' ' . ($section['suffix'] ?? '');
                        @endphp
                        <div class="flex items-center justify-between gap-4 rounded-lg bg-white px-4 py-3 shadow-sm">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-black">{{ data_get($product, 'name', '-') }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Stok {{ number_format((int) data_get($product, 'stock', 0), 0, ',', '.') }}
                                    @if (data_get($product, 'reviews_count') !== null)
                                        <span class="mx-1">.</span>
                                        {{ number_format((int) data_get($product, 'reviews_count', 0), 0, ',', '.') }} ulasan
                                    @endif
                                </p>
                            </div>
                            <span class="shrink-0 text-sm font-black text-primary-dark">{{ $metricText }}</span>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-border-strong bg-white px-4 py-8 text-center text-sm text-gray-500">
                            Belum ada data
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</section>
