@props([
    'navLinks' => [],
])

@php
    $cartItemsCount = 0;

    if (auth()->check() && auth()->user()->role === 'customer') {
        $cart = auth()->user()->cart;
        $cartItemsCount = $cart ? $cart->cartItems()->sum('quantity') : 0;
    }
@endphp

<header class="sticky top-0 z-40 w-full border-b border-border-soft bg-white/95 backdrop-blur-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            <div class="flex shrink-0 items-center gap-3">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIRACAS"
                        class="h-10 w-10 rounded-full object-cover shadow-sm">
                    <span class="hidden text-2xl font-bold tracking-[0.06em] text-primary sm:block">
                        SIRACAS
                    </span>
                </a>
            </div>

            <nav class="hidden md:flex md:items-center md:gap-6 lg:gap-8 text-sm font-medium text-muted-dark"
                aria-label="Navigasi utama">
                @foreach ($navLinks as $link)
                    @php
                        // Memeriksa apakah URL saat ini cocok dengan route navigasi
                        $linkPath = parse_url($link['route'], PHP_URL_PATH) ?: $link['route'];
                        $isActive = $linkPath === '/' ? request()->is('/') : request()->is(trim($linkPath, '/'));
                    @endphp
                    <a href="{{ $link['route'] }}"
                        class="relative py-2 transition-colors duration-200 hover:text-primary {{ $isActive ? 'text-primary' : '' }}">
                        {{ $link['nav'] }}
                        @if ($isActive)
                            <span class="absolute inset-x-0 bottom-0 h-0.5 rounded-t-full bg-primary"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 sm:gap-3 text-muted-dark">
                <a href="{{ route('cart.index') }}"
                    class="relative inline-flex items-center justify-center rounded-full p-2 transition-colors hover:bg-primary-soft focus:outline-none focus:ring-2 focus:ring-border-strong"
                    aria-label="Keranjang">
                    <x-icons.cart class="h-5 w-5 text-muted-dark" />
                    @if ($cartItemsCount > 0)
                        <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-accent px-1.5 text-[11px] font-bold leading-none text-white">
                            {{ $cartItemsCount > 99 ? '99+' : $cartItemsCount }}
                        </span>
                    @endif
                </a>

                <a href="#"
                    class="relative inline-flex items-center justify-center rounded-full p-2 transition-colors hover:bg-primary-soft focus:outline-none focus:ring-2 focus:ring-border-strong"
                    aria-label="Notifikasi">
                    <x-icons.bell class="h-5 w-5 text-muted-dark" />
                </a>

                <div class="ml-2 h-6 w-px bg-border-soft"></div>
                @auth
                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape.window="open = false" type="button"
                            class="inline-flex items-center justify-center rounded-full border border-border-strong bg-white overflow-hidden h-9 w-9 transition-all hover:border-primary hover:bg-primary-soft focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            id="user-menu-button">

                            @if (auth()->user()->foto_profil)
                                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                                    class="h-9 w-9 object-cover">
                            @else
                                <div class="flex items-center justify-center h-9 w-9">
                                    <x-icons.user class="h-5 w-5 text-muted-dark" />
                                </div>
                            @endif

                        </button>

                        <div x-show="open" @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]"
                            x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]"
                            class="absolute right-0 mt-3 w-48 origin-top-right overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                            style="display: none;" role="menu" aria-orientation="vertical"
                            aria-labelledby="user-menu-button" tabindex="-1">

                            <div class="border-b border-gray-100 px-4 py-3">
                                <p class="text-sm text-gray-500">Login sebagai</p>
                                <p class="truncate text-sm font-medium text-gray-900">{{ auth()->user()->username ?? 'User' }}
                                </p>
                            </div>

                            <div class="py-1" role="none">
                                <a href="{{ route('customer.profile') }}"
                                    class="flex px-4 py-2.5 text-sm text-muted-dark transition-colors hover:bg-primary-soft hover:text-primary"
                                    role="menuitem" tabindex="-1">
                                    Lihat Akun
                                </a>
                                <form action="/logout" method="post" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="flex w-full text-left px-4 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                                        role="menuitem" tabindex="-1">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="/register"
                            class="hidden rounded-full px-4 py-2 text-sm font-semibold text-text-body transition hover:bg-primary-soft hover:text-primary sm:inline-flex">
                            Register
                        </a>
                        <a href="/login"
                            class="inline-flex items-center justify-center rounded-full border border-border-strong px-4 py-2 text-sm font-semibold text-muted-dark shadow-sm transition hover:border-primary hover:bg-primary-soft focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-1">
                            Login
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <nav class="flex overflow-x-auto border-t border-border-soft py-3 md:hidden [&::-webkit-scrollbar]:hidden"
            aria-label="Navigasi mobile">
            <div class="flex min-w-full gap-6 px-2 text-sm font-medium text-muted-dark">
                @foreach ($navLinks as $link)
                    @php
                        $linkPath = parse_url($link['route'], PHP_URL_PATH) ?: $link['route'];
                        $isActive = $linkPath === '/' ? request()->is('/') : request()->is(trim($linkPath, '/'));
                    @endphp
                    <a href="{{ $link['route'] }}"
                        class="whitespace-nowrap transition-colors hover:text-primary {{ $isActive ? 'text-primary font-semibold' : '' }}">
                        {{ $link['nav'] }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</header>
