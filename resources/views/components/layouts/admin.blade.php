<x-layouts.public>
    <div x-data="{
        sidebarOpen: JSON.parse(localStorage.getItem('siracas_admin_sidebar_open') ?? 'true')
    }"
        x-effect="localStorage.setItem('siracas_admin_sidebar_open', JSON.stringify(sidebarOpen))"
        class="min-h-screen bg-[#f7f7f7] text-black">

        @php
            $menus = [
                ['name' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
                ['name' => 'Akun', 'route' => 'admin.profile', 'icon' => 'profile'],
                ['name' => 'Akun Customer', 'route' => 'admin.customer.index', 'icon' => 'customer'],
                ['name' => 'Produk', 'route' => 'home', 'icon' => 'product'],
                ['name' => 'Transaksi', 'route' => 'home', 'icon' => 'transaksi'],
                ['name' => 'Riwayat Transaksi', 'route' => 'home', 'icon' => 'riwayat'],
                ['name' => 'Ulasan', 'route' => 'home', 'icon' => 'ulasan'],
                ['name' => 'Chat', 'route' => 'home', 'icon' => 'chat'],
                ['name' => 'Notifikasi', 'route' => 'home', 'icon' => 'notifikasi'],
                ['name' => 'Laporan Operasional', 'route' => 'home', 'icon' => 'laporan'],
            ];
        @endphp

        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#a98c77] px-3 py-7 text-white shadow-lg transition-all duration-300"
            :class="sidebarOpen ? 'w-[260px]' : 'w-[76px]'">
            <div class="mb-5 flex items-center" :class="sidebarOpen ? 'h-10 gap-4 px-2' : 'flex-col gap-3 px-0'">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SIRACAS"
                    class="h-8 w-8 shrink-0 rounded-full bg-white object-cover">

                <div x-show="sidebarOpen" x-transition.opacity class="min-w-0 flex-1">
                    <h1 class="truncate text-2xl font-bold tracking-wide">SIRACAS</h1>
                </div>

                <button type="button" @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-white transition hover:bg-white/15"
                    aria-label="Buka tutup sidebar">
                    <svg class="h-7 w-7 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-0.5 -0.5 16 16"
                        stroke-linecap="round" stroke-linejoin="round" stroke="currentColor"
                        id="Sidebar--Streamline-Mynaui" height="100%" width="100%">
                        <desc>
                            Sidebar Streamline Icon: https://streamlinehq.com
                        </desc>
                        <path
                            d="M5.625 2.1875v10.625M1.875 5.875c0 -1.4000000000000001 0 -2.1 0.2725 -2.6350000000000002a2.5 2.5 0 0 1 1.0925 -1.0925C3.775 1.875 4.475 1.875 5.875 1.875h3.25c1.4000000000000001 0 2.1 0 2.6350000000000002 0.2725a2.5 2.5 0 0 1 1.0925 1.0925C13.125 3.775 13.125 4.475 13.125 5.875v3.25c0 1.4000000000000001 0 2.1 -0.2725 2.6350000000000002a2.5 2.5 0 0 1 -1.0925 1.0925C11.225000000000001 13.125 10.525 13.125 9.125 13.125H5.875c-1.4000000000000001 0 -2.1 0 -2.6350000000000002 -0.2725a2.5 2.5 0 0 1 -1.0925 -1.0925C1.875 11.225000000000001 1.875 10.525 1.875 9.125z"
                            stroke-width="1"></path>
                    </svg>
                </button>
            </div>

            <div x-show="sidebarOpen" x-transition.opacity class="mb-4 px-3 text-sm text-white/95">
                Main menu
            </div>

            <nav class="flex flex-1 flex-col gap-2 text-base">
                @foreach ($menus as $menu)
                    @php
                        $active = request()->routeIs($menu['route'] . '*');
                    @endphp

                    <a href="{{ route($menu['route']) }}" title="{{ $menu['name'] }}"
                        class="group flex h-10 items-center rounded-md transition-all duration-200 {{ $active ? 'bg-white/25 font-semibold text-white' : 'text-white/95 hover:bg-white/15 hover:text-white' }}"
                        :class="sidebarOpen ? 'justify-start gap-3 px-4' : 'justify-center px-0'">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <x-dynamic-component :component="'icons.menus.' . $menu['icon']" />
                        </span>
                        <span x-show="sidebarOpen" x-transition.opacity class="truncate">
                            {{ $menu['name'] }}
                        </span>
                    </a>
                @endforeach
            </nav>

            @auth
                <div class="border-t border-white/20 pt-4">
                    <div class="flex items-center" :class="sidebarOpen ? 'justify-between gap-3 px-1' : 'justify-center'">
                        <div class="flex min-w-0 items-center gap-3">
                            <img src="{{ auth()->user()->foto_profil
                                ? asset('storage/' . auth()->user()->foto_profil)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->username) }}"
                                alt="Foto {{ auth()->user()->username }}"
                                class="h-10 w-10 shrink-0 rounded-full object-cover">

                            <div x-show="sidebarOpen" x-transition.opacity class="min-w-0 text-sm">
                                <p class="truncate font-semibold">{{ auth()->user()->username }}</p>
                                <p class="truncate text-xs capitalize text-white/70">{{ auth()->user()->role }}</p>
                            </div>
                        </div>

                        <form x-show="sidebarOpen" x-transition.opacity method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" title="Logout"
                                class="rounded-lg p-2 text-white/80 transition hover:bg-red-500/20 hover:text-red-100">
                                <x-icons.logout />
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </aside>

        <main class="min-h-screen transition-all duration-300" :class="sidebarOpen ? 'pl-[260px]' : 'pl-[76px]'">
            {{ $slot }}
        </main>
    </div>
</x-layouts.public>
