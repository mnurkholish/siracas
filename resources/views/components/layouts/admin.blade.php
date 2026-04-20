<x-layouts.public>
    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-[#8B735F] text-white shadow-md p-4 flex flex-col">

            <!-- HEADER -->
            <div class="flex items-center gap-3 mb-6">
                <img src="{{ asset('images/logo.png') }}" class="w-8 h-8">
                <h5 class="text-lg font-semibold tracking-wide">
                    SIRACAS
                </h5>
            </div>

            <!-- MENU -->
            <nav class="flex flex-col gap-1 text-sm flex-1">

                @php
                    $menus = [
                        ['name' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
                        ['name' => 'Akun', 'route' => 'home', 'icon' => 'profile'],
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

                @foreach ($menus as $menu)
                    @php
                        $active = request()->routeIs($menu['route'] . '*');
                    @endphp

                    <a href="{{ route($menu['route']) ?? '#' }}"
                        class="flex items-center w-full p-2 rounded-lg transition-all
                        
                        {{ $active ? 'bg-white/25 text-white font-medium' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">

                        <div class="mr-4">
                            <x-dynamic-component :component="'icons.menus.' . $menu['icon']" class="w-5 h-5" />
                        </div>

                        {{ $menu['name'] }}
                    </a>
                @endforeach

            </nav>

            <!-- USER PROFILE -->
            @auth
                <div class="mt-4 pt-4 border-t border-white/20">

                    <div class="flex items-center justify-between">

                        <!-- KIRI: FOTO + INFO -->
                        <div class="flex items-center gap-3">

                            <!-- FOTO -->
                            <img src="{{ auth()->user()->foto_profil
                                ? asset('storage/' . auth()->user()->foto_profil)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->username) }}"
                                class="w-10 h-10 rounded-full object-cover">

                            <!-- INFO -->
                            <div class="flex flex-col text-sm">
                                <span class="font-semibold">
                                    {{ auth()->user()->username }}
                                </span>

                                <span class="text-white/70 text-xs capitalize">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                        </div>

                        <!-- KANAN: LOGOUT ICON -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" title="Logout"
                                class="p-2 rounded-lg text-white/80 hover:bg-red-500/20 hover:text-red-200 transition">

                                <x-icons.logout class="w-5 h-5" />
                            </button>
                        </form>

                    </div>

                </div>
            @endauth

        </aside>

        <!-- CONTENT -->
        <main class="flex-1 bg-gray-100 p-6">
            {{ $slot }}
        </main>

    </div>
</x-layouts.public>
