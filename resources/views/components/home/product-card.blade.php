@props([
    'image',
    'name',
    'description',
    'price',
    'url' => '#',
    'buttonText' => 'Lihat Detail',
])

<article
    class="group flex h-full flex-col overflow-hidden rounded-[18px] border border-[#dfd4c9] bg-white shadow-[0_14px_28px_rgba(145,117,97,0.08)] transition duration-300 hover:-translate-y-1.5 hover:shadow-[0_22px_38px_rgba(145,117,97,0.16)]">
    <div class="overflow-hidden bg-[#eee6df]">
        <img src="{{ $image }}" alt="{{ $name }}"
            class="h-36 w-full object-cover transition duration-500 group-hover:scale-105">
    </div>

    <div class="flex flex-1 flex-col p-4">
        <h3 class="text-sm font-bold text-[#6b584d]">{{ $name }}</h3>
        <p class="mt-1 text-xs leading-5 text-[#9d8d82]">{{ $description }}</p>

        <div class="mt-3 flex items-center gap-1 text-[11px] text-[#d39b3d]">
            @for ($i = 0; $i < 5; $i++)
                <span>★</span>
            @endfor
        </div>

        <div class="mt-3 flex items-end justify-between gap-3">
            <div>
                <p class="text-[11px] text-[#aa9d94]">Harga mulai</p>
                <p class="text-base font-extrabold text-[#b37323]">{{ $price }}</p>
            </div>

            <a href="{{ $url }}"
                class="rounded-full bg-[#a6866d] px-4 py-2 text-[11px] font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#8f715b]">
                {{ $buttonText }}
            </a>
        </div>
    </div>
</article>
