<x-layouts.admin title="{{ filled($review->admin_reply) ? 'Edit Balasan' : 'Balas Review' }}"
    subtitle="Tulis balasan admin untuk review customer.">
    <section class="grid gap-6 lg:grid-cols-[1fr_420px]">
        <article class="card p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <img src="{{ $review->product?->foto ? asset('storage/' . $review->product->foto) : asset('images/banners/banner-2.webp') }}"
                    alt="{{ $review->product?->nama_produk ?? 'Produk' }}"
                    class="h-28 w-28 rounded-lg border border-border-soft object-cover">

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="font-black text-muted-dark">{{ $review->product?->nama_produk ?? 'Produk tidak tersedia' }}</h2>
                        <span class="rounded-full {{ filled($review->admin_reply) ? 'bg-success-soft text-success' : 'bg-warning-soft text-warning' }} px-3 py-1 text-xs font-bold">
                            {{ filled($review->admin_reply) ? 'Sudah dibalas' : 'Belum dibalas' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm font-semibold text-muted">
                        {{ $review->user?->username ?? 'Customer' }} • {{ $review->created_at->format('d M Y H:i') }}
                    </p>
                    <div class="mt-2 flex items-center gap-1 text-accent">
                        @for ($star = 1; $star <= 5; $star++)
                            <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <p class="mt-3 text-sm leading-7 text-muted-dark">{{ $review->isi }}</p>

                    @if ($review->foto)
                        <img src="{{ asset('storage/' . $review->foto) }}"
                            alt="Foto review {{ $review->product?->nama_produk ?? 'produk' }}"
                            class="mt-4 h-28 w-28 rounded-lg border border-border-soft object-cover">
                    @endif
                </div>
            </div>
        </article>

        <form method="POST" action="{{ route('admin.reviews.reply', array_merge([$review], request()->query())) }}"
            class="card p-5">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert-danger mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <label for="admin-reply-{{ $review->id }}" class="form-label">Balasan Admin</label>
            <textarea id="admin-reply-{{ $review->id }}" name="admin_reply" class="form-control textarea-control bg-white"
                placeholder="Tulis balasan untuk customer...">{{ old('admin_reply', $review->admin_reply) }}</textarea>
            <p class="mt-2 text-xs font-semibold text-muted">Kosongkan lalu simpan untuk menghapus balasan.</p>

            @if (filled($review->admin_reply))
                <div class="mt-4 rounded-lg border border-border-soft bg-surface px-4 py-3">
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-primary">Balasan saat ini</p>
                    <p class="mt-2 text-sm leading-7 text-muted-dark">{{ $review->admin_reply }}</p>
                    <p class="mt-2 text-xs font-semibold text-muted">
                        {{ $review->adminReplier?->username ?? 'Admin' }} • {{ $review->admin_replied_at?->format('d M Y H:i') }}
                    </p>
                </div>
            @endif

            <div class="mt-5 flex flex-wrap gap-3">
                <x-button type="submit" size="lg">
                    Simpan Balasan
                </x-button>
                @if (filled($review->admin_reply))
                    <button type="submit"
                        class="inline-flex h-11 items-center justify-center rounded-lg border border-red-200 bg-white px-5 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                        onclick="document.getElementById('admin-reply-{{ $review->id }}').value = ''">
                        Hapus Balasan
                    </button>
                @endif
                <x-button :href="route('admin.reviews.index', request()->query())" variant="secondary" size="lg">
                    Kembali
                </x-button>
            </div>
        </form>
    </section>
</x-layouts.admin>
