@props([
    'status' => null,
    'variant' => null,
])

@php
    $statusClasses = [
        'pending' => 'bg-warning-soft text-warning',
        'paid' => 'bg-info-soft text-info',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'completed' => 'bg-success-soft text-success',
        'cancelled' => 'bg-danger-soft text-red-700',
        'expired' => 'bg-gray-100 text-gray-700',
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
    {{ $slot->isEmpty() ? $status : $slot }}
</span>
