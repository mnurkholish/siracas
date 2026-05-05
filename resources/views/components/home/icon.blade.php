@props([
    'name',
    'alt' => '',
    'class' => 'h-4 w-4',
])

@php
    $iconAttributes = $attributes->class($class)->merge([
        'xmlns' => 'http://www.w3.org/2000/svg',
        'viewBox' => '0 0 24 24',
        'fill' => 'currentColor',
        'aria-hidden' => $alt === '' ? 'true' : null,
        'role' => $alt !== '' ? 'img' : null,
        'aria-label' => $alt !== '' ? $alt : null,
    ]);
@endphp

@switch($name)
    @case('instagram')
        <svg {{ $iconAttributes }}>
            <path
                d="M7.75 2h8.5A5.76 5.76 0 0 1 22 7.75v8.5A5.76 5.76 0 0 1 16.25 22h-8.5A5.76 5.76 0 0 1 2 16.25v-8.5A5.76 5.76 0 0 1 7.75 2Zm0 2A3.75 3.75 0 0 0 4 7.75v8.5A3.75 3.75 0 0 0 7.75 20h8.5A3.75 3.75 0 0 0 20 16.25v-8.5A3.75 3.75 0 0 0 16.25 4h-8.5Zm8.75 2.25a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" />
        </svg>
    @break

    @case('tiktok')
        <svg {{ $iconAttributes }}>
            <path
                d="M16.6 5.82A5.86 5.86 0 0 1 13.55 1h-3.1v13.1a2.74 2.74 0 1 1-2-2.64V8.31a5.85 5.85 0 1 0 5.1 5.79V7.45a8.94 8.94 0 0 0 5.22 1.67V6.03a5.83 5.83 0 0 1-2.17-.21Z" />
        </svg>
    @break

    @case('linkedin')
        <svg {{ $iconAttributes }}>
            <path
                d="M19 3A2 2 0 0 1 21 5v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14ZM8.34 17.5V9.78H5.76v7.72h2.58ZM7.05 8.73a1.49 1.49 0 1 0 0-2.98 1.49 1.49 0 0 0 0 2.98Zm11.2 8.77v-4.24c0-2.27-1.21-3.32-2.82-3.32a2.43 2.43 0 0 0-2.2 1.21h-.04V9.78h-2.47v7.72h2.58v-3.82c0-1 .19-1.98 1.43-1.98 1.23 0 1.25 1.15 1.25 2.05v3.75h2.27Z" />
        </svg>
    @break
@endswitch
