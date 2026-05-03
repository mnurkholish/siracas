@props([
    'status' => null,
    'variant' => null,
])

@php
    $statusClasses = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'paid' => 'bg-blue-100 text-blue-800',
        'processing' => 'bg-indigo-100 text-indigo-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'expired' => 'bg-gray-100 text-gray-700',
    ];
    $variantClasses = [
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'muted' => 'bg-gray-100 text-gray-700',
    ];
    $color = $status
        ? ($statusClasses[$status] ?? $variantClasses['muted'])
        : ($variantClasses[$variant] ?? $variantClasses['muted']);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-bold capitalize ' . $color]) }}>
    {{ $slot->isEmpty() ? $status : $slot }}
</span>
