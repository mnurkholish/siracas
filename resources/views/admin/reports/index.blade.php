@php
    $months = collect(range(1, 12))->mapWithKeys(fn ($month) => [
        $month => \Carbon\Carbon::create(null, $month, 1)->translatedFormat('F'),
    ]);
    $years = range(now()->year, now()->year - 5);
    $exportUrl = route('admin.reports.export', ['bulan' => $period['month'], 'tahun' => $period['year']]);
@endphp

<x-layouts.admin title="Laporan" subtitle="Ringkasan performa penjualan, produk, customer, dan transaksi SIRACAS.">
    <x-slot:actions>
        <form method="GET" action="{{ route('admin.reports.index') }}"
            class="flex w-full flex-col gap-2 rounded-lg border border-border-soft bg-white p-3 shadow-sm sm:w-auto sm:flex-row sm:items-center">
            <select name="bulan" class="form-control input-control sm:w-40">
                @foreach ($months as $value => $label)
                    <option value="{{ $value }}" @selected((int) $period['month'] === (int) $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="tahun" class="form-control input-control sm:w-28">
                @foreach ($years as $year)
                    <option value="{{ $year }}" @selected((int) $period['year'] === (int) $year)>
                        {{ $year }}
                    </option>
                @endforeach
            </select>

            <x-button type="submit" size="lg" variant="secondary">
                Terapkan Filter
            </x-button>

            <x-button :href="$exportUrl" size="lg">
                Download XLSX
            </x-button>
        </form>
    </x-slot:actions>

    <div class="space-y-6">
        @include('admin.reports.partials._summary-cards')
        @include('admin.reports.partials._charts')
        @include('admin.reports.partials._product-stats')
        @include('admin.reports.partials._customer-stats')
        @include('admin.reports.partials._status-table')
    </div>
</x-layouts.admin>
