@props([
    'reviews' => [],
])

<section class="bg-white px-6 py-16 lg:px-10 lg:py-24">
    <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <p class="text-xs font-bold uppercase tracking-[0.4em] text-muted-light">Testimoni</p>
            <h2 class="mt-2 text-3xl font-black text-primary-dark">Review Pelanggan Kami</h2>
            <p class="mt-4 text-sm leading-7 text-muted">
                Jangan hanya percaya kata kami. Ini beberapa cerita dari pelanggan kami setelah memakai produk organik dan cacing dari SIRACAS.
            </p>
        </div>

        <div class="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($reviews as $review)
                <div data-reveal style="transition-delay: {{ min($loop->index * 70, 280) }}ms">
                    <x-home.review-card :name="$review['name']" :initials="$review['initials']" :review="$review['review']" />
                </div>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center" data-reveal>
            <a href="#"
                class="rounded-full bg-primary px-8 py-3 text-sm font-bold text-white transition hover:bg-primary-dark">
                More
            </a>
        </div>
    </div>
</section>
