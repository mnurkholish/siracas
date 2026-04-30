<section class="relative h-180 overflow-hidden">

    <div id="slider" class="flex h-full transition-transform duration-700 ease-in-out">

        <!-- SLIDE 1 -->
        <x-home.hero-slide image="{{ asset('images/banners/banner-2.webp') }}" subtitle="Selamat Datang di" title="SIRACAS"
            description="Rumah alam yang menghadirkan produk cacing, pupuk organik, dan olahan bernilai guna."
            :showButton="true" buttonText="Belanja Sekarang" buttonLink="#produk" />

        <!-- SLIDE 2 -->
        <x-home.hero-slide image="{{ asset('images/banners/banner-2.webp') }}" subtitle="Produk Unggulan"
            title="Pupuk Kascing" description="Pupuk organik berkualitas tinggi untuk meningkatkan kesuburan tanah." />

    </div>

</section>
