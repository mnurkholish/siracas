@props([
    'name',
    'alt' => '',
    'class' => 'h-4 w-4',
])

<img src="{{ asset('images/icons/' . $name . '.svg') }}" alt="{{ $alt }}" {{ $attributes->class($class) }}>
