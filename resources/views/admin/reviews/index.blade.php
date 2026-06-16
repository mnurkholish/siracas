<x-layouts.admin title="Review" subtitle="Daftar review customer.">
    <x-slot:actions>
        <form method="GET" action="{{ route('admin.reviews.index') }}" class="relative w-full sm:w-[430px]">
            @if (request('reply_status'))
                <input type="hidden" name="reply_status" value="{{ request('reply_status') }}">
            @endif
            @if (request('rating'))
                <input type="hidden" name="rating" value="{{ request('rating') }}">
            @endif

            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari customer, produk, atau isi review"
                class="h-12 w-full rounded-full border-0 bg-white px-6 pr-14 text-sm text-gray-700 shadow-sm outline-none placeholder:text-gray-500 focus:ring-2 focus:ring-border-strong sm:px-8 sm:text-base">
            <button type="submit"
                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-primary-dark"
                aria-label="Cari review">
                <i class="bi bi-search text-lg"></i>
            </button>
        </form>
    </x-slot:actions>

    <section class="table-wrap">
        <div
            class="flex flex-col gap-4 border-b border-gray-100 bg-white px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap gap-2">
                @php
                    $baseFilter = array_filter([
                        'search' => request('search'),
                        'rating' => request('rating'),
                    ]);
                    $allUrl =
                        $baseFilter === []
                            ? route('admin.reviews.index')
                            : route('admin.reviews.index') . '?' . http_build_query($baseFilter);
                @endphp

                <a href="{{ $allUrl }}"
                    class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold transition {{ blank(request('reply_status')) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-soft hover:text-primary-dark' }}">
                    Semua
                </a>

                @foreach ($replyStatusOptions as $value => $label)
                    @php
                        $query = array_filter([
                            'search' => request('search'),
                            'rating' => request('rating'),
                            'reply_status' => $value,
                        ]);
                    @endphp
                    <a href="{{ route('admin.reviews.index') . '?' . http_build_query($query) }}"
                        class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold transition {{ request('reply_status') === $value ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-soft hover:text-primary-dark' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.reviews.index') }}"
                class="flex flex-col gap-2 sm:flex-row sm:items-center">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if (request('reply_status'))
                    <input type="hidden" name="reply_status" value="{{ request('reply_status') }}">
                @endif

                <select name="rating" class="form-control input-control sm:w-40">
                    <option value="">Semua rating</option>
                    @for ($star = 5; $star >= 1; $star--)
                        <option value="{{ $star }}" @selected((string) request('rating') === (string) $star)>
                            {{ $star }} bintang
                        </option>
                    @endfor
                </select>
                <x-button type="submit" variant="secondary" size="lg">
                    Filter
                </x-button>
                <a href="{{ route('admin.reviews.index') }}"
                    class="inline-flex h-11 items-center justify-center rounded-lg border border-border-strong bg-white px-5 text-sm font-semibold text-muted-dark transition hover:bg-primary-soft">
                    Reset
                </a>
            </form>
        </div>

        <div class="divide-y divide-gray-100 bg-white">
            @forelse ($reviews as $review)
                <article class="p-5">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-start">
                            <img src="{{ $review->product?->foto ? asset('storage/' . $review->product->foto) : asset('images/banners/banner-2.webp') }}"
                                alt="{{ $review->product?->nama_produk ?? 'Produk' }}"
                                class="h-24 w-24 rounded-lg border border-border-soft object-cover">

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="font-black text-muted-dark">
                                        {{ $review->product?->nama_produk ?? 'Produk tidak tersedia' }}</h2>
                                    <span
                                        class="rounded-full {{ filled($review->admin_reply) ? 'bg-success-soft text-success' : 'bg-warning-soft text-warning' }} px-3 py-1 text-xs font-bold">
                                        {{ filled($review->admin_reply) ? 'Sudah dibalas' : 'Belum dibalas' }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm font-semibold text-muted">
                                    {{ $review->user?->username ?? 'Customer' }} •
                                    {{ $review->created_at->format('d M Y H:i') }}
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

                                @if (filled($review->admin_reply))
                                    <div class="mt-4 rounded-lg border border-border-soft bg-surface px-4 py-3">
                                        <p class="text-xs font-black uppercase tracking-[0.25em] text-primary">Balasan
                                            saat ini</p>
                                        <p class="mt-2 text-sm leading-7 text-muted-dark">{{ $review->admin_reply }}
                                        </p>
                                        <p class="mt-2 text-xs font-semibold text-muted">
                                            {{ $review->adminReplier?->username ?? 'Admin' }} •
                                            {{ $review->admin_replied_at?->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0">
                            <x-button :href="route('admin.reviews.create', array_merge([$review], request()->query()))" size="sm">
                                {{ filled($review->admin_reply) ? 'Edit Balasan' : 'Balas Review' }}
                            </x-button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-5 py-12 text-center text-gray-500">
                    Belum ada review yang sesuai filter.
                </div>
            @endforelse
        </div>

        @if ($reviews->hasPages())
            <div class="border-t border-gray-100 bg-white px-4 py-6">
                <x-pagination :paginator="$reviews" />
            </div>
        @endif
    </section>
</x-layouts.admin>
