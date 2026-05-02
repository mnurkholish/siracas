<x-layouts.public title="Beranda - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main>
        <x-home.hero-section />

        <section class="bg-[#f7f2ed] px-6 py-10 lg:px-10">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-4 rounded-lg border border-[#e4d7cc] bg-white px-5 py-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_18px_34px_rgba(145,117,97,0.12)] sm:flex-row sm:items-center sm:justify-between sm:px-7"
                data-reveal>
                <div class="min-w-0">
                    <p class="text-xs font-bold uppercase tracking-[0.35em] text-[#b7a69a]">Halo,
                        {{ auth()->user()->username }}</p>
                    <h1 class="mt-2 text-2xl font-black text-[#6f5a4c]">Selamat datang kembali di SIRACAS</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#8f8178]">
                        Temukan produk cacing dan olahan organik pilihan, lalu kelola akun Anda dari menu profil.
                    </p>
                </div>

                <a href="{{ route('customer.profile') }}"
                    class="inline-flex h-11 items-center justify-center rounded-lg bg-[#9e836f] px-5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#8a725f]">
                    Lihat Akun
                </a>
            </div>

            <div class="mx-auto mt-5 grid max-w-7xl gap-4 md:grid-cols-3" x-data="{ active: 'produk' }">
                <a href="{{ route('customer.product.index') }}" @mouseenter="active = 'produk'"
                    class="group rounded-lg border border-[#e4d7cc] bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#c9b5a7] hover:shadow-[0_18px_34px_rgba(145,117,97,0.12)]"
                    data-reveal style="transition-delay: 80ms">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#b7a69a]">Produk</p>
                            <h2 class="mt-2 text-lg font-black text-[#6f5a4c]">Lihat Katalog</h2>
                        </div>
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-full bg-[#f1e7df] text-[#8c725f] transition group-hover:bg-[#a6866d] group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.25 7.5 12 3 3.75 7.5m16.5 0L12 12m8.25-4.5v9L12 21m0-9L3.75 7.5m8.25 4.5v9m-8.25-13.5v9L12 21" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-[#8f8178]">Cari cacing, kascing, dan olahan organik yang tersedia.</p>
                </a>

                <a href="{{ route('cart.index') }}" @mouseenter="active = 'keranjang'"
                    class="group rounded-lg border border-[#e4d7cc] bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#c9b5a7] hover:shadow-[0_18px_34px_rgba(145,117,97,0.12)]"
                    data-reveal style="transition-delay: 160ms">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#b7a69a]">Keranjang</p>
                            <h2 class="mt-2 text-lg font-black text-[#6f5a4c]">Pesanan Saya</h2>
                        </div>
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-full bg-[#f1e7df] text-[#8c725f] transition group-hover:bg-[#a6866d] group-hover:text-white">
                            <x-icons.cart class="h-5 w-5" />
                        </span>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-[#8f8178]">Simpan produk pilihan dan atur jumlah pesanan sebelum checkout.</p>
                </a>

                <a href="{{ route('customer.profile') }}" @mouseenter="active = 'akun'"
                    class="group rounded-lg border border-[#e4d7cc] bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#c9b5a7] hover:shadow-[0_18px_34px_rgba(145,117,97,0.12)]"
                    data-reveal style="transition-delay: 240ms">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#b7a69a]">Akun</p>
                            <h2 class="mt-2 text-lg font-black text-[#6f5a4c]">Profil Customer</h2>
                        </div>
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-full bg-[#f1e7df] text-[#8c725f] transition group-hover:bg-[#a6866d] group-hover:text-white">
                            <x-icons.user class="h-5 w-5" />
                        </span>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-[#8f8178]">Periksa data diri agar proses transaksi nanti lebih lancar.</p>
                </a>
            </div>
        </section>

        <x-home.about-section />
        <x-home.products-section :products="$products" :all-link="route('customer.product.index')" />
        <x-home.reviews-section :reviews="$reviews" />
    </main>

    <x-home.footer />
</x-layouts.public>
