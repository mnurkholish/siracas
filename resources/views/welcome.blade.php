@php
    $products = [
        [
            'name' => 'Cacing Tanah Segar',
            'description' => 'Cacing tanah segar berkualitas untuk kebutuhan budidaya, pakan, dan pengolahan organik.',
            'price' => 'Rp4.000',
            'image' => asset('images/hero.png'),
        ],
        [
            'name' => 'Cacing Tanah Kering',
            'description' => 'Cacing tanah kering yang higienis, cocok untuk campuran pakan bernutrisi tinggi.',
            'price' => 'Rp4.000',
            'image' => asset('images/hero.png'),
        ],
        [
            'name' => 'Pupuk Kascing',
            'description' => 'Pupuk organik padat hasil olahan cacing untuk membantu tanah lebih subur dan gembur.',
            'price' => 'Rp10.000',
            'image' => asset('images/hero.png'),
        ],
        [
            'name' => 'Tepung Cacing',
            'description' => 'Tepung cacing bernutrisi tinggi untuk pakan ternak, ikan, dan unggas.',
            'price' => 'Rp10.000',
            'image' => asset('images/hero.png'),
        ],
    ];

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
            'nav' => 'Tentang Kami',
            'route' => '#tentang',
        ],
        [
            'nav' => 'Produk',
            'route' => '#',
        ],
    ];
@endphp

<x-layouts.public title="SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main>
        <x-home.hero-section />
        <x-home.about-section />
        <x-home.products-section :products="$products" />
        <x-home.reviews-section :reviews="$reviews" />
    </main>

    <x-home.footer />
</x-layouts.public>
