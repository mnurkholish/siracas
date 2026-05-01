<section x-data="{ active: 0, total: 2, timer: null }"
    x-init="timer = setInterval(() => active = (active + 1) % total, 5200)"
    @mouseenter="clearInterval(timer)" @mouseleave="timer = setInterval(() => active = (active + 1) % total, 5200)"
    class="relative h-[620px] overflow-hidden sm:h-[680px] lg:h-[720px]">

    <div id="slider" class="flex h-full transition-transform duration-700 ease-in-out"
        :style="`transform: translateX(-${active * 100}%);`">

        <!-- SLIDE 1 -->
        <x-home.hero-slide image="{{ asset('images/banners/banner-2.webp') }}" subtitle="Selamat Datang di" title="SIRACAS"
            description="Rumah alam yang menghadirkan produk cacing, pupuk organik, dan olahan bernilai guna."
            :showButton="true" buttonText="Belanja Sekarang" buttonLink="#produk" />

        <!-- SLIDE 2 -->
        <x-home.hero-slide image="{{ asset('images/banners/banner-2.webp') }}" subtitle="Produk Unggulan"
            title="Pupuk Kascing" description="Pupuk organik berkualitas tinggi untuk meningkatkan kesuburan tanah." />

    </div>

    <div class="absolute bottom-8 left-1/2 z-30 flex -translate-x-1/2 items-center gap-3">
        <template x-for="index in total" :key="index">
            <button type="button" @click="active = index - 1"
                class="h-2.5 rounded-full transition-all duration-300"
                :class="active === index - 1 ? 'w-9 bg-white' : 'w-2.5 bg-white/55 hover:bg-white/80'"
                :aria-label="`Tampilkan slide ${index}`"></button>
        </template>
    </div>

    <button type="button" @click="active = active === 0 ? total - 1 : active - 1"
        class="absolute left-4 top-1/2 z-30 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/35 bg-white/15 text-white backdrop-blur transition hover:bg-white/25 sm:inline-flex"
        aria-label="Slide sebelumnya">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19 8 12l7-7" />
        </svg>
    </button>

    <button type="button" @click="active = (active + 1) % total"
        class="absolute right-4 top-1/2 z-30 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/35 bg-white/15 text-white backdrop-blur transition hover:bg-white/25 sm:inline-flex"
        aria-label="Slide berikutnya">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7" />
        </svg>
    </button>

</section>
