@auth
    @if (auth()->user()->role === 'customer')
        @php
            $popupNotification = auth()->user()->unreadNotifications()->latest()->first();
        @endphp

        @if ($popupNotification)
            @php
                $payload = $popupNotification->data;
                $popupUrl = $payload['url'] ?? null;
            @endphp

            <div id="notification-popup"
                class="fixed bottom-5 right-5 z-[80] w-[calc(100%-2.5rem)] max-w-sm overflow-hidden rounded-lg border border-border-strong bg-white shadow-2xl shadow-black/15 sm:bottom-6 sm:right-6"
                data-read-url="{{ route('notifications.read', $popupNotification) }}">
                <div class="flex items-start gap-4 p-4">
                    <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft text-primary-dark">
                        <x-icons.bell class="h-5 w-5" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-muted-light">
                            {{ str_replace('_', ' ', $payload['type'] ?? 'notifikasi') }}
                        </p>
                        <h2 class="mt-1 text-sm font-black text-text-body">{{ $payload['title'] ?? 'Notifikasi' }}</h2>
                        <p class="mt-1 line-clamp-3 text-sm leading-6 text-muted">{{ $payload['message'] ?? '-' }}</p>

                        @if ($popupUrl)
                            <a href="{{ $popupUrl }}"
                                class="mt-3 inline-flex text-sm font-bold text-primary-dark transition hover:text-primary">
                                Lihat detail
                            </a>
                        @endif
                    </div>

                    <button type="button" id="notification-popup-close"
                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                        aria-label="Tutup notifikasi">
                        &times;
                    </button>
                </div>
            </div>

            <script>
                (() => {
                    const popup = document.getElementById('notification-popup');
                    const closeButton = document.getElementById('notification-popup-close');

                    if (!popup || !closeButton) {
                        return;
                    }

                    closeButton.addEventListener('click', async () => {
                        try {
                            await fetch(popup.dataset.readUrl, {
                                method: 'PATCH',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                            });
                        } finally {
                            popup.remove();
                        }
                    });
                })();
            </script>
        @endif
    @endif
@endauth
