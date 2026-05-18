@auth
    @if (auth()->user()->role === 'customer')
        @php
            $popupNotification = auth()->user()->unreadNotifications()->latest()->first();
        @endphp

        @if ($popupNotification)
            @php
                $payload = $popupNotification->data;
            @endphp

            <div id="notification-popup"
                class="fixed bottom-5 right-5 z-[80] w-[calc(100%-2.5rem)] max-w-sm overflow-hidden rounded-lg border border-border-strong/80 bg-white/85 shadow-2xl shadow-black/15 backdrop-blur-md transition duration-300 sm:bottom-6 sm:right-6"
                data-auto-dismiss-ms="5000">
                <div class="flex items-start gap-4 p-4">
                    <div
                        class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft/80 text-primary-dark">
                        <x-icons.bell class="h-5 w-5" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-muted-light">
                            {{ str_replace('_', ' ', $payload['type'] ?? 'notifikasi') }}
                        </p>
                        <h2 class="mt-1 text-sm font-black text-text-body">{{ $payload['title'] ?? 'Notifikasi' }}</h2>
                        <p class="mt-1 line-clamp-3 text-sm leading-6 text-muted">{{ $payload['message'] ?? '-' }}</p>

                        <a href="{{ route('notifications.index') }}"
                            class="mt-3 inline-flex text-sm font-bold text-primary-dark transition hover:text-primary">
                            Lihat detail
                        </a>
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

                    const dismissPopup = () => {
                        if (popup.dataset.dismissing === 'true') {
                            return;
                        }

                        popup.dataset.dismissing = 'true';
                        popup.classList.add('translate-y-3', 'opacity-0');
                        window.setTimeout(() => popup.remove(), 300);
                    };

                    closeButton.addEventListener('click', dismissPopup);
                    window.setTimeout(dismissPopup, Number(popup.dataset.autoDismissMs || 6500));
                })();
            </script>
        @endif
    @endif
@endauth
