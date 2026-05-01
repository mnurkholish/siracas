<x-layouts.public title="Beranda - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main>
        <x-home.hero-section />

        <section class="bg-[#f7f2ed] px-6 py-10 lg:px-10">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-4 rounded-lg border border-[#e4d7cc] bg-white px-5 py-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-7">
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-[0.35em] text-[#b7a69a]">Halo,
                        {{ auth()->user()->username }}</p>
                    <h1 class="mt-2 text-2xl font-black text-[#6f5a4c]">Selamat datang kembali di SIRACAS</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8f8178]">
                        Temukan produk cacing dan olahan organik pilihan, lalu kelola akun Anda dari menu profil.
                    </p>
                </div>

                <a href="{{ route('customer.profile') }}"
                    class="inline-flex h-11 items-center justify-center rounded-lg bg-[#9e836f] px-5 text-sm font-semibold text-white transition hover:bg-[#8a725f]">
                    Lihat Akun
                </a>
            </div>
        </section>

        <x-home.about-section />
        <x-home.products-section :products="$products" :all-link="route('customer.product.index')" />
        <x-home.reviews-section :reviews="$reviews" />
    </main>

    <x-home.footer />
</x-layouts.public>
