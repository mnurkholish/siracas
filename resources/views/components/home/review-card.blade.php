@props([
    'name',
    'initials',
    'review',
])

<article class="h-full rounded-[18px] border border-border bg-white p-5 shadow-[0_14px_24px_rgba(145,117,97,0.06)] transition duration-300 hover:-translate-y-1 hover:border-border-strong hover:shadow-[0_20px_34px_rgba(145,117,97,0.12)]">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-sm font-bold text-primary-dark">
                {{ $initials }}
            </div>
            <p class="text-sm font-bold text-muted-dark">{{ $name }}</p>
        </div>

        <div class="flex items-center gap-1 text-xs text-amber-500">
            @for ($i = 0; $i < 5; $i++)
                <span>★</span>
            @endfor
        </div>
    </div>

    <p class="mt-4 text-sm leading-6 text-muted">
        {{ $review }}
    </p>
</article>
