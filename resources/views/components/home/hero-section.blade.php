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

    <!-- NAV -->
    <button id="prevBtn" onclick="prevSlide()"
        class="nav-btn absolute left-5 top-1/2 -translate-y-1/2 bg-white/80 p-3 rounded-full shadow z-50 transition-all duration-500">
        < </button>

            <button id="nextBtn" onclick="nextSlide()"
                class="nav-btn absolute right-5 top-1/2 -translate-y-1/2 bg-white/80 p-3 rounded-full shadow z-50 transition-all duration-500">
                >
            </button>

</section>
