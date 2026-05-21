@php
    $navLinks = [
        [
            'nav' => 'Beranda',
            'route' => '/',
        ],
        [
            'nav' => 'Produk',
            'route' => route('products.index'),
        ],
        [
            'nav' => 'Transaksi',
            'route' => route('transactions.index'),
        ],
    ];
@endphp

<x-layouts.public title="SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main>
        <x-home.hero-section />
        <x-home.about-section />
        <x-home.products-section :products="$products" :allLink="route('products.index')" />
        <x-home.reviews-section :reviews="$reviews" />
    </main>

    <x-home.footer />
</x-layouts.public>
