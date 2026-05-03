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
        'primary' => 'bg-[#9e836f] text-white shadow-sm hover:bg-[#8a725f] focus:ring-[#c9b5a7]',
        'secondary' => 'border border-[#d8c9bc] bg-white text-[#6f5a4c] hover:bg-[#f7f1eb] focus:ring-[#d8c9bc]',
        'danger' => 'bg-[#ef3f46] text-white hover:bg-red-600 focus:ring-red-200',
        'danger-soft' => 'border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 focus:ring-red-200',
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
