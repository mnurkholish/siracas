@props([
    'navLinks' => [],
])

<header class="sticky top-0 z-40 w-full border-b border-[#e7ddd4] bg-white/95 backdrop-blur-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">

            <div class="flex shrink-0 items-center gap-3">
                <a href="/" class="flex items-center gap-3 transition-transform hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SIRACAS"
                        class="h-10 w-10 rounded-full object-cover shadow-sm">
                    <span class="hidden text-2xl font-bold tracking-[0.06em] text-primary sm:block">
                        SIRACAS
                    </span>
                </a>
            </div>

            <nav class="hidden md:flex md:items-center md:gap-6 lg:gap-8 text-sm font-medium text-[#4d443f]"
                aria-label="Navigasi utama">
                @foreach ($navLinks as $link)
                    @php
                        // Memeriksa apakah URL saat ini cocok dengan route navigasi
                        $isActive = request()->is(trim($link['route'], '/'));
                    @endphp
                    <a href="{{ $link['route'] }}"
                        class="relative py-2 transition-colors duration-200 hover:text-primary {{ $isActive ? 'text-primary' : '' }}">
                        {{ $link['nav'] }}
                        @if ($isActive)
                            <span class="absolute inset-x-0 bottom-0 h-0.5 rounded-t-full bg-[#d3b49f]"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2 sm:gap-3 text-[#4d443f]">
                <a href="#"
                    class="relative inline-flex items-center justify-center rounded-full p-2 transition-colors hover:bg-[#f7f1eb] focus:outline-none focus:ring-2 focus:ring-[#d8c9bc]"
                    aria-label="Keranjang">
                    <x-icons.cart class="h-5 w-5 text-[#4d443f]" />
                    {{-- Opsional: Badge Notifikasi Keranjang --}}
                    {{-- <span class="absolute right-1 top-1 flex h-2 w-2 rounded-full bg-red-500"></span> --}}
                </a>

                <a href="#"
                    class="relative inline-flex items-center justify-center rounded-full p-2 transition-colors hover:bg-[#f7f1eb] focus:outline-none focus:ring-2 focus:ring-[#d8c9bc]"
                    aria-label="Notifikasi">
                    <x-icons.bell class="h-5 w-5 text-[#4d443f]" />
                </a>

                <div class="ml-2 h-6 w-px bg-[#e7ddd4]"></div>
                @auth
                    <div class="relative ml-2" x-data="{ open: false }">
                        <button @click="open = !open" @keydown.escape.window="open = false" type="button"
                            class="inline-flex items-center justify-center rounded-full border border-[#d8c9bc] bg-white overflow-hidden h-9 w-9 transition-all hover:border-[#a9896f] hover:bg-[#f7f1eb] focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            id="user-menu-button">

                            @if (auth()->user()->foto_profil)
                                <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}"
                                    class="h-9 w-9 object-cover">
                            @else
                                <div class="flex items-center justify-center h-9 w-9">
                                    <x-icons.user class="h-5 w-5 text-[#4d443f]" />
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
                                <p class="truncate text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'User' }}
                                </p>
                            </div>

                            <div class="py-1" role="none">
                                <a href="{{ route('customer.profile') }}"
                                    class="flex px-4 py-2.5 text-sm text-[#4d443f] transition-colors hover:bg-[#f7f1eb] hover:text-[#a98e79]"
                                    role="menuitem" tabindex="-1">
                                    Edit Profil
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
                            class="hidden rounded-full px-4 py-2 text-sm font-semibold text-[#6f5a4c] transition hover:bg-[#f7f1eb] hover:text-primary sm:inline-flex">
                            Register
                        </a>
                        <a href="/login"
                            class="inline-flex items-center justify-center rounded-full border border-[#d8c9bc] px-4 py-2 text-sm font-semibold text-[#4d443f] shadow-sm transition hover:border-[#a9896f] hover:bg-[#f7f1eb] focus:outline-none focus:ring-2 focus:ring-[#a9896f] focus:ring-offset-1">
                            Login
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <nav class="flex overflow-x-auto border-t border-[#e7ddd4] py-3 md:hidden [&::-webkit-scrollbar]:hidden"
            aria-label="Navigasi mobile">
            <div class="flex min-w-full gap-6 px-2 text-sm font-medium text-[#4d443f]">
                @foreach ($navLinks as $link)
                    @php
                        $isActive = request()->is(trim($link['route'], '/'));
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
