@props([
    'reviews' => [],
])

<section class="bg-white px-6 py-16 lg:px-10 lg:py-24">
    <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-xs font-bold uppercase tracking-[0.4em] text-[#b7a69a]">Testimoni</p>
            <h2 class="mt-2 text-3xl font-black text-[#8c725f]">Review Pelanggan Kami</h2>
            <p class="mt-4 text-sm leading-7 text-[#9a8b81]">
                Jangan hanya percaya kata kami. Ini beberapa cerita dari pelanggan kami setelah memakai produk organik dan cacing dari SIRACAS.
            </p>
        </div>

        <div class="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($reviews as $review)
                <x-home.review-card :name="$review['name']" :initials="$review['initials']" :review="$review['review']" />
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            <a href="#"
                class="rounded-full bg-[#a6866d] px-8 py-3 text-sm font-bold text-white transition hover:bg-[#8f715b]">
                More
            </a>
        </div>
    </div>
</section>
