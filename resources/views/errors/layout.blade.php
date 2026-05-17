@php
    $title = trim($__env->yieldContent('title')) ?: 'Terjadi Kesalahan';
    $code = trim($__env->yieldContent('code')) ?: 'Error';
@endphp

<x-layouts.public :title="$title . ' - SIRACAS'">
    <header class="border-b border-border-soft bg-white/95">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SIRACAS"
                    class="h-10 w-10 rounded-full object-cover shadow-sm">
                <span class="text-xl font-black tracking-[0.08em] text-primary sm:text-2xl">
                    SIRACAS
                </span>
            </a>

            <a href="{{ route('home') }}"
                class="inline-flex h-10 items-center justify-center rounded-lg border border-border-strong bg-white px-4 text-sm font-semibold text-muted-dark shadow-sm transition hover:border-primary hover:bg-primary-soft hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                Beranda
            </a>
        </div>
    </header>

    <main class="flex flex-1 items-center px-5 py-16 sm:px-6 lg:px-10">
        <section class="mx-auto grid w-full max-w-5xl items-center gap-10 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="text-center lg:text-left">
                <p class="text-xs font-black uppercase tracking-[0.35em] text-muted-light">Error</p>
                <p class="mt-4 text-[8rem] font-black leading-none text-primary sm:text-[11rem] lg:text-[15rem]">
                    {{ $code }}
                </p>
                <div class="mx-auto mt-6 h-1 w-20 rounded-full bg-accent lg:mx-0"></div>
            </div>

            <div class="max-w-2xl">
                <p class="text-xs font-black uppercase tracking-[0.35em] text-muted-light">SIRACAS</p>
                <h1 class="mt-3 text-3xl font-black leading-tight text-text-body sm:text-4xl">
                    @yield('heading')
                </h1>
                <p class="mt-4 text-sm leading-7 text-muted sm:text-base">
                    @yield('message')
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('home') }}"
                        class="inline-flex h-12 items-center justify-center rounded-lg bg-primary px-6 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                        Kembali ke Beranda
                    </a>
                    <button type="button" onclick="history.back()"
                        class="inline-flex h-12 items-center justify-center rounded-lg border border-border-strong bg-white px-6 text-sm font-bold text-muted-dark shadow-sm transition hover:bg-primary-soft focus:outline-none focus:ring-2 focus:ring-border-strong">
                        Kembali
                    </button>
                </div>
            </div>
        </section>
    </main>
</x-layouts.public>
