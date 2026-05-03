@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'block' => false,
])

@php
    $base = 'inline-flex items-center justify-center rounded-lg font-semibold transition focus:outline-none focus:ring-2';
    $variants = [
        'primary' => 'bg-primary text-white shadow-sm hover:bg-primary-dark focus:ring-primary/30',
        'secondary' => 'border border-border-strong bg-white text-text-body hover:bg-primary-soft focus:ring-border-strong',
        'danger' => 'bg-danger text-white hover:bg-danger-dark focus:ring-red-200',
        'danger-soft' => 'border border-red-200 bg-danger-soft text-red-600 hover:bg-red-100 focus:ring-red-200',
        'ghost' => 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 focus:ring-gray-200',
    ];
    $sizes = [
        'sm' => 'h-9 px-4 text-xs font-bold',
        'md' => 'h-10 px-5 text-sm',
        'lg' => 'h-11 px-5 text-sm',
        'xl' => 'h-12 px-5 text-sm font-bold',
    ];
    $classes = trim($base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($block ? 'w-full' : ''));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
