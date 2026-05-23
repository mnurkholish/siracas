@php
    $cards = [
        ['label' => 'Total Pendapatan Bulan Ini', 'value' => 'Rp' . number_format($summary['net_revenue'], 0, ',', '.'), 'tone' => 'text-success'],
        ['label' => 'Total Transaksi Bulan Ini', 'value' => number_format($summary['transaction_count'], 0, ',', '.'), 'tone' => 'text-info'],
        ['label' => 'Total Produk Terjual Bulan Ini', 'value' => number_format($summary['products_sold'], 0, ',', '.'), 'tone' => 'text-primary-dark'],
        ['label' => 'Total Refund/Garansi Bulan Ini', 'value' => 'Rp' . number_format($summary['refund_total'], 0, ',', '.'), 'tone' => 'text-danger'],
        ['label' => 'Estimasi Pendapatan Bulan Ini', 'value' => 'Rp' . number_format($summary['estimated_revenue'], 0, ',', '.'), 'tone' => 'text-accent'],
    ];
@endphp

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    @foreach ($cards as $card)
        <article class="rounded-lg border border-border bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase text-gray-500">{{ $card['label'] }}</p>
            <p class="mt-4 break-words text-2xl font-black {{ $card['tone'] }}">
                {{ $card['value'] }}
            </p>
        </article>
    @endforeach
</section>
