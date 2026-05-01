@props([
    'title' => null,
    'subtitle' => null,
    'maxWidth' => 'max-w-7xl',
])

<x-layouts.public :title="$title ? $title . ' - SIRACAS' : 'Admin - SIRACAS'">
    <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen bg-[#f7f7f7] text-black">
        @php
            $menus = [
                ['name' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'],
                ['name' => 'Akun', 'route' => 'admin.profile', 'icon' => 'profile'],
                ['name' => 'Akun Customer', 'route' => 'admin.customer.index', 'icon' => 'customer'],
                ['name' => 'Produk', 'route' => 'admin.product.index', 'icon' => 'product'],
                ['name' => 'Transaksi', 'route' => 'home', 'icon' => 'transaksi'],
                ['name' => 'Riwayat Transaksi', 'route' => 'home', 'icon' => 'riwayat'],
                ['name' => 'Ulasan', 'route' => 'home', 'icon' => 'ulasan'],
                ['name' => 'Chat', 'route' => 'home', 'icon' => 'chat'],
                ['name' => 'Notifikasi', 'route' => 'home', 'icon' => 'notifikasi'],
                ['name' => 'Laporan Operasional', 'route' => 'home', 'icon' => 'laporan'],
            ];
        @endphp

        <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/35 lg:hidden"></div>

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[260px] max-w-[82vw] -translate-x-full flex-col bg-[#a98c77] px-3 py-5 text-white shadow-lg transition-transform duration-200 lg:translate-x-0 lg:py-7"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="mb-5 flex h-10 items-center justify-between gap-3 px-2">
                <a href="/" class="flex min-w-0 items-center gap-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIRACAS"
                        class="h-8 w-8 shrink-0 rounded-full bg-white object-cover">
                    <h1 class="truncate text-2xl font-bold tracking-wide">SIRACAS</h1>
                </a>

                <button type="button" @click="sidebarOpen = false"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-white/80 transition hover:bg-white/15 hover:text-white lg:hidden"
                    aria-label="Tutup menu">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mb-4 px-3 text-sm text-white/95">
                Main menu
            </div>

            <nav class="flex flex-1 flex-col gap-2 overflow-y-auto pr-1 text-base">
                @foreach ($menus as $menu)
                    @php
                        $active = request()->routeIs($menu['route'] . '*');
                    @endphp

                    <a href="{{ route($menu['route']) }}" title="{{ $menu['name'] }}" @click="sidebarOpen = false"
                        class="group flex min-h-10 items-center justify-start gap-3 rounded-md px-4 py-2 transition-all duration-200 {{ $active ? 'bg-white/25 font-semibold text-white' : 'text-white/95 hover:bg-white/15 hover:text-white' }}">
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
                <div class="mt-4 border-t border-white/20 pt-4">
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

        <div class="lg:pl-[260px]">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-[#e8dfd8] bg-white/95 px-4 backdrop-blur lg:hidden">
                <button type="button" @click="sidebarOpen = true"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-[#e8dfd8] text-[#6f5a4c] transition hover:bg-[#f7f1eb]"
                    aria-label="Buka menu admin">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                </button>

                <div class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIRACAS" class="h-8 w-8 rounded-full object-cover">
                    <span class="truncate text-lg font-bold tracking-wide text-[#6f5a4c]">SIRACAS</span>
                </div>

                <div class="h-10 w-10"></div>
            </header>

            <main class="min-h-screen">
                <div class="mx-auto w-full {{ $maxWidth }} px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    @if ($title || isset($actions))
                        <header class="mb-7 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0">
                                @if ($title)
                                    <h1 class="truncate text-2xl font-bold text-black">{{ $title }}</h1>
                                @endif

                                @if ($subtitle)
                                    <p class="mt-2 text-sm leading-6 text-gray-500">{{ $subtitle }}</p>
                                @endif
                            </div>

                            @isset($actions)
                                <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                                    {{ $actions }}
                                </div>
                            @endisset
                        </header>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</x-layouts.public>
