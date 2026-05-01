@props([
    'products' => [],
    'allLink' => '#',
])

<section id="produk" class="bg-[#eee6df] px-6 py-16 lg:px-10 lg:py-20">
    <div class="mx-auto max-w-7xl">
        <div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-reveal>
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.4em] text-[#b7a69a]">Produk Unggulan</p>
                <h2 class="mt-2 text-3xl font-black text-[#6f5a4c]">Produk Kita</h2>
            </div>

            <a href="{{ $allLink }}"
                class="inline-flex w-fit items-center rounded-full border border-[#b89f8d] px-5 py-2 text-sm font-semibold text-[#8c725f] transition hover:bg-white">
                Lihat Semua
            </a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($products as $product)
                <div data-reveal style="transition-delay: {{ min($loop->index * 80, 240) }}ms">
                    <x-home.product-card :image="$product['image']" :name="$product['name']" :description="$product['description']" :price="$product['price']" :url="$product['url'] ?? '#'" />
                </div>
            @endforeach
        </div>
    </div>
</section>
