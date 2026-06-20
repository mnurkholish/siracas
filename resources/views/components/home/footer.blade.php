<footer class="mt-auto bg-secondary px-6 py-14 text-sm text-muted lg:px-10">
    <div class="mx-auto grid max-w-7xl gap-10 md:grid-cols-2 xl:grid-cols-5">
        <div>
            <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-body">SIRACAS</h3>
            <div class="mt-4 space-y-2">
                <p><a href="{{ route('home') }}">Beranda</a></p>
                <p><a href="{{ route('home') }}#tentang">Tentang Kami</a></p>
                <p><a href="{{ route('products.index') }}">Produk</a></p>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-body">Layanan</h3>
            <div class="mt-4 space-y-2">
                <p>
                    @if (! empty($adminWhatsappUrl))
                        <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener">Hubungi Kami</a>
                    @else
                        Hubungi Kami
                    @endif
                </p>
                <p><a href="{{ route('cart.index') }}">Keranjang</a></p>
                <p><a href="{{ route('transactions.index') }}">Pesanan Saya</a></p>
                <p><a href="{{ route('reviews.index') }}">Review Saya</a></p>
            </div>
        </div>

        <div class="flex items-center xl:justify-center">
            <p class="text-3xl font-black tracking-[0.18em] text-primary">SIRACAS</p>
        </div>

        <div>
            <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-body">Jelajahi</h3>
            <div class="mt-4 space-y-2">
                <p><a href="{{ route('home') }}#produk">Produk Unggulan</a></p>
                <p><a href="{{ route('products.index') }}">Produk</a></p>
                <p><a href="{{ route('transactions.index') }}">Transaksi</a></p>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-body">Akun</h3>
            <div class="mt-4 space-y-2">
                @auth
                    <p><a href="{{ route('profile') }}">Profil</a></p>
                    <p><a href="{{ route('notifications.index') }}">Notifikasi</a></p>
                @else
                    <p><a href="{{ route('login') }}">Login</a></p>
                    <p><a href="{{ route('register') }}">Register</a></p>
                @endauth
            </div>
        </div>
    </div>

    <div
        class="mx-auto mt-12 flex max-w-7xl flex-col gap-4 border-t border-border-strong pt-6 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-muted">© 2026 Siracas. All rights reserved.</p>
        <div class="flex items-center gap-3 text-xs text-muted">
            <span>Hubungi kami</span>
            @if (! empty($adminWhatsappUrl))
                <a href="{{ $adminWhatsappUrl }}" target="_blank" rel="noopener"
                    class="rounded-full border border-border-strong p-2 transition hover:bg-white/60"
                    aria-label="WhatsApp">
                    <x-home.icon name="whatsapp" alt="WhatsApp" class="h-4 w-4" />
                </a>
            @endif
        </div>
    </div>
</footer>
