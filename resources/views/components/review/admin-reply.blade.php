@props([
    'review',
])

@if (filled($review->admin_reply))
    <div class="mt-4 rounded-lg border border-border-soft bg-white px-4 py-3">
        <p class="text-xs font-black uppercase tracking-[0.25em] text-primary">Balasan Admin</p>
        <p class="mt-2 text-sm leading-7 text-muted-dark">{{ $review->admin_reply }}</p>
        @if ($review->admin_replied_at)
            <p class="mt-2 text-xs font-semibold text-muted">
                {{ $review->admin_replied_at->format('d M Y') }}
            </p>
        @endif
    </div>
@endif
