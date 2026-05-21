@props([
    'status' => null,
    'variant' => null,
])

@php
    $statusClasses = [
        'menunggu_pembayaran' => 'bg-warning-soft text-warning',
        'dibayar' => 'bg-info-soft text-info',
        'diproses' => 'bg-indigo-100 text-indigo-800',
        'dikirim' => 'bg-primary-soft text-primary-dark',
        'diterima' => 'bg-info-soft text-info',
        'selesai' => 'bg-success-soft text-success',
        'dibatalkan' => 'bg-danger-soft text-red-700',
        'kedaluwarsa' => 'bg-gray-100 text-gray-700',
        'tidak_ada' => 'bg-gray-100 text-gray-700',
        'diajukan' => 'bg-warning-soft text-warning',
        'ditolak' => 'bg-danger-soft text-red-700',
    ];
    $statusLabels = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'dibayar' => 'Dibayar',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'diterima' => 'Diterima',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'kedaluwarsa' => 'Kedaluwarsa',
        'tidak_ada' => 'Tidak Ada',
        'diajukan' => 'Diajukan',
        'ditolak' => 'Ditolak',
    ];
    $variantClasses = [
        'success' => 'bg-success-soft text-success',
        'warning' => 'bg-warning-soft text-warning',
        'danger' => 'bg-danger-soft text-red-700',
        'info' => 'bg-info-soft text-info',
        'muted' => 'bg-gray-100 text-gray-700',
    ];
    $color = $status
        ? ($statusClasses[$status] ?? $variantClasses['muted'])
        : ($variantClasses[$variant] ?? $variantClasses['muted']);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-bold capitalize ' . $color]) }}>
    {{ $slot->isEmpty() ? ($statusLabels[$status] ?? $status) : $slot }}
</span>
