<x-layouts.public>
    <div class="min-h-screen bg-[#f7f7f7] text-black">
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

        <aside class="fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col bg-[#a98c77] px-3 py-7 text-white shadow-lg">
            <div class="mb-5 flex h-10 items-center gap-4 px-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SIRACAS"
                    class="h-8 w-8 shrink-0 rounded-full bg-white object-cover">

                <h1 class="truncate text-2xl font-bold tracking-wide">SIRACAS</h1>
            </div>

            <div class="mb-4 px-3 text-sm text-white/95">
                Main menu
            </div>

            <nav class="flex flex-1 flex-col gap-2 text-base">
                @foreach ($menus as $menu)
                    @php
                        $active = request()->routeIs($menu['route'] . '*');
                    @endphp

                    <a href="{{ route($menu['route']) }}" title="{{ $menu['name'] }}"
                        class="group flex h-10 items-center justify-start gap-3 rounded-md px-4 transition-all duration-200 {{ $active ? 'bg-white/25 font-semibold text-white' : 'text-white/95 hover:bg-white/15 hover:text-white' }}">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center">
                            <x-dynamic-component :component="'icons.menus.' . $menu['icon']" />
                        </span>
                        <span class="truncate">
                            {{ $menu['name'] }}
                        </span>
                    </a>
                @endforeach
            </nav>

            @auth
                <div class="border-t border-white/20 pt-4">
                    <div class="flex items-center justify-between gap-3 px-1">
                        <div class="flex min-w-0 items-center gap-3">
                            <img src="{{ auth()->user()->foto_profil
                                ? asset('storage/' . auth()->user()->foto_profil)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->username) }}"
                                alt="Foto {{ auth()->user()->username }}"
                                class="h-10 w-10 shrink-0 rounded-full object-cover">

                            <div class="min-w-0 text-sm">
                                <p class="truncate font-semibold">{{ auth()->user()->username }}</p>
                                <p class="truncate text-xs capitalize text-white/70">{{ auth()->user()->role }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
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

        <main class="min-h-screen pl-[260px]">
            {{ $slot }}
        </main>
    </div>
</x-layouts.public>
