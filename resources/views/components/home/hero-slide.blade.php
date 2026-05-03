<div class="relative z-0 h-full min-w-full">

    <!-- BACKGROUND -->
    <img src="{{ asset($image) }}" class="absolute inset-0 h-full w-full scale-105 object-cover transition duration-[1800ms]"
        alt="{{ $title }}">
    <div class="absolute inset-0 bg-gradient-to-r from-stone-900/80 via-text-body/45 to-transparent"></div>

    <!-- CONTENT -->
    <div class="relative z-20 mx-auto flex h-full max-w-7xl items-center px-6 text-white lg:px-10">

        <div class="max-w-xl drop-shadow-lg" data-reveal>

            <p class="mb-4 text-sm uppercase tracking-[0.45em]">
                {{ $subtitle }}
            </p>

            <h1 class="text-4xl font-black italic sm:text-5xl lg:text-6xl">
                {{ $title }}
            </h1>

            <p class="mt-5 text-sm sm:text-base leading-7">
                {{ $description }}
            </p>

            @if ($showButton ?? false)
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ $buttonLink ?? '#' }}"
                        class="pulse-soft rounded-full bg-white px-6 py-3 text-sm font-bold text-primary-dark shadow transition hover:-translate-y-1 hover:bg-primary-soft">
                        {{ $buttonText ?? 'Belanja' }}
                    </a>

                    <a href="#tentang"
                        class="rounded-full border border-white px-6 py-3 text-sm font-semibold transition hover:-translate-y-1 hover:bg-white/10">
                        Lihat Profil
                    </a>
                </div>
            @endif

        </div>

    </div>
</div>
