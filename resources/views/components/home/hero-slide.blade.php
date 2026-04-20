<div class="relative min-w-full h-full z-0">

    <!-- BACKGROUND -->
    <img src="{{ asset($image) }}" class="absolute inset-0 w-full h-full object-cover">

    <!-- CONTENT -->
    <div class="relative z-20 flex items-center h-full max-w-7xl mx-auto px-6 lg:px-10 text-white">

        <div class="max-w-md drop-shadow-lg">

            <p class="mb-4 text-sm uppercase tracking-[0.45em]">
                {{ $subtitle }}
            </p>

            <h1 class="text-4xl sm:text-5xl font-black italic">
                {{ $title }}
            </h1>

            <p class="mt-5 text-sm sm:text-base leading-7">
                {{ $description }}
            </p>

            @if ($showButton ?? false)
                <div class="mt-8 flex gap-4">
                    <a href="{{ $buttonLink ?? '#' }}"
                        class="bg-white text-[#8d715e] px-6 py-3 rounded-full text-sm font-bold shadow hover:-translate-y-1 transition">
                        {{ $buttonText ?? 'Belanja' }}
                    </a>

                    <a href="#tentang"
                        class="border border-white px-6 py-3 rounded-full text-sm font-semibold hover:bg-white/10">
                        Lihat Profil
                    </a>
                </div>
            @endif

        </div>

    </div>
</div>
