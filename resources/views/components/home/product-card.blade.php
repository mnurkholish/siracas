@props([
    'image',
    'name',
    'description',
    'price',
    'url' => '#',
    'buttonText' => 'Lihat Detail',
])

<article
    class="group flex h-full flex-col overflow-hidden rounded-[18px] border border-border bg-white shadow-[0_14px_28px_rgba(145,117,97,0.08)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_22px_38px_rgba(145,117,97,0.16)]">
    <div class="overflow-hidden bg-secondary">
        <img src="{{ $image }}" alt="{{ $name }}"
            class="h-36 w-full object-cover transition duration-500 group-hover:scale-105">
    </div>

    <div class="flex flex-1 flex-col p-4">
        <h3 class="text-sm font-bold text-muted-dark">{{ $name }}</h3>
        <p class="mt-1 text-xs leading-5 text-muted">{{ $description }}</p>

        <div class="mt-3 flex items-center gap-1 text-[11px] text-amber-500">
            @for ($i = 0; $i < 5; $i++)
                <span>★</span>
            @endfor
        </div>

        <div class="mt-3 flex items-end justify-between gap-3">
            <div>
                <p class="text-[11px] text-muted-light">Harga mulai</p>
                <p class="text-base font-extrabold text-accent">{{ $price }}</p>
            </div>

            <a href="{{ $url }}"
                class="rounded-full bg-primary px-4 py-2 text-[11px] font-semibold text-white transition hover:-translate-y-0.5 hover:bg-primary-dark">
                {{ $buttonText }}
            </a>
        </div>
    </div>
</article>
