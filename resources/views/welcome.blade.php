@php
    $reviews = [
        [
            'name' => 'Kholis',
            'initials' => 'Kh',
            'review' =>
                'Cacing segar saya terima besar-besar dan bagus kualitasnya. Kemasan juga aman dan tidak berbau berlebihan.',
        ],
        [
            'name' => 'Lian',
            'initials' => 'Li',
            'review' =>
                'Sudah beberapa kali order di sini dan selalu puas. Pelayanannya cepat, respon admin baik, dan produknya konsisten.',
        ],
        [
            'name' => 'Thalia',
            'initials' => 'Th',
            'review' =>
                'Pupuk kascing sangat membantu tanaman saya jadi lebih subur. Teksturnya halus dan mudah digunakan.',
        ],
        [
            'name' => 'Bilqis',
            'initials' => 'Bi',
            'review' =>
                'Tepung cacing berkualitas, cocok dipakai untuk usaha pakan saya. Tekstur halus dan stok sering tersedia.',
        ],
        [
            'name' => 'Budi',
            'initials' => 'Bu',
            'review' =>
                'Kascing sangat ringan dan membuat drainase media tanam jauh lebih baik. Proses pemesanan juga mudah dan tidak ribet.',
        ],
        [
            'name' => 'Sugeng',
            'initials' => 'Su',
            'review' =>
                'Cacing saya diterima masih dalam kondisi aktif dan segar. Sangat puas dengan kebersihan pengiriman.',
        ],
    ];

    $navLinks = [
        [
            'nav' => 'Beranda',
            'route' => '/',
        ],
        [
            'nav' => 'Produk',
            'route' => route('customer.product.index'),
        ],
        [
            'nav' => 'Transaksi',
            'route' => '#',
        ],
    ];
@endphp

<x-layouts.public title="SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main>
        <x-home.hero-section />
        <x-home.about-section />
        <x-home.products-section :products="$products" :allLink="route('customer.product.index')" />
        <x-home.reviews-section :reviews="$reviews" />
    </main>

    <x-home.footer />
</x-layouts.public>
