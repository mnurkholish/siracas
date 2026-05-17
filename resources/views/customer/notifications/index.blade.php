@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('dashboard')],
        ['nav' => 'Produk', 'route' => route('product.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
        ['nav' => 'Review Saya', 'route' => route('reviews.index')],
    ];
@endphp

<x-layouts.public title="Notifikasi - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="page">
        <section class="page-container">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">Notifikasi</p>
                    <h1 class="page-title">Riwayat Notifikasi</h1>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <form action="{{ route('notifications.readAll') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-button type="submit" variant="secondary" size="lg">
                            Tandai Semua Dibaca
                        </x-button>
                    </form>

                    <form action="{{ route('notifications.destroyAll') }}" method="POST" class="delete-all-notifications-form">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" variant="danger-soft" size="lg">
                            Hapus Semua
                        </x-button>
                    </form>
                </div>
            </div>

            <div class="mt-8 space-y-3">
                @forelse ($notifications as $notification)
                    @php
                        $payload = $notification->data;
                        $isUnread = is_null($notification->read_at);
                    @endphp

                    <article class="rounded-lg border {{ $isUnread ? 'border-primary bg-white shadow-sm' : 'border-border bg-white/75' }} p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-badge :variant="$isUnread ? 'info' : 'muted'">
                                        {{ $isUnread ? 'Unread' : 'Dibaca' }}
                                    </x-badge>
                                    <span class="text-xs font-bold uppercase tracking-[0.24em] text-muted-light">
                                        {{ str_replace('_', ' ', $payload['type'] ?? 'notifikasi') }}
                                    </span>
                                </div>

                                <h2 class="mt-3 text-lg font-black text-text-body">{{ $payload['title'] ?? 'Notifikasi' }}</h2>
                                <p class="mt-2 text-sm leading-6 text-muted">{{ $payload['message'] ?? '-' }}</p>
                                <p class="mt-3 text-xs font-semibold text-muted-light">
                                    {{ $notification->created_at->format('d M Y H:i') }}
                                </p>
                            </div>

                            <div class="flex shrink-0 flex-wrap gap-2">
                                @if (! empty($payload['url']))
                                    <x-button :href="$payload['url']" variant="secondary" size="sm">
                                        Detail
                                    </x-button>
                                @endif

                                @if ($isUnread)
                                    <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <x-button type="submit" size="sm">
                                            Tandai Dibaca
                                        </x-button>
                                    </form>
                                @endif

                                <form action="{{ route('notifications.destroy', $notification) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="danger-soft" size="sm">
                                        Hapus
                                    </x-button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <h2 class="text-lg font-black text-text-body">Belum ada notifikasi</h2>
                        <p class="mt-2 text-sm text-muted">Semua update pesanan dan informasi dari admin akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            @if ($notifications->hasPages())
                <div class="mt-6 rounded-lg border border-border bg-white px-5 py-4 shadow-sm">
                    {{ $notifications->links() }}
                </div>
            @endif
        </section>
    </main>

    <script>
        document.querySelectorAll('.delete-all-notifications-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus semua notifikasi?',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: themeColor('primary'),
                    cancelButtonColor: themeColor('danger'),
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    <x-home.footer />
</x-layouts.public>
