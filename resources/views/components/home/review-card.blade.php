@props([
    'name',
    'initials',
    'review',
])

<article class="h-full rounded-[18px] border border-[#ece1d7] bg-white p-5 shadow-[0_14px_24px_rgba(145,117,97,0.06)] transition duration-300 hover:-translate-y-1 hover:border-[#d8c3b4] hover:shadow-[0_20px_34px_rgba(145,117,97,0.12)]">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#e5dbd2] text-sm font-bold text-[#8f7766]">
                {{ $initials }}
            </div>
            <p class="text-sm font-bold text-[#6b584d]">{{ $name }}</p>
        </div>

        <div class="flex items-center gap-1 text-xs text-[#e3ac46]">
            @for ($i = 0; $i < 5; $i++)
                <span>★</span>
            @endfor
        </div>
    </div>

    <p class="mt-4 text-sm leading-6 text-[#8f8178]">
        {{ $review }}
    </p>
</article>
