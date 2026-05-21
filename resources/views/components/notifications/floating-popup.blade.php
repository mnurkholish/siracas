@auth
    @php
        $user = auth()->user();
        $popupNotification = $user->unreadNotifications()->latest()->first();
        $notificationIndexUrl = null;
        $popupTitle = null;
        $popupMessage = null;

        if ($user->role === 'customer') {
            $notificationIndexUrl = route('notifications.index');
        }

        if ($user->role === 'admin') {
            $notificationIndexUrl = route('admin.notifications.index');
            $needsOngkirCount = \App\Models\Transaction::query()
                ->where('status', 'menunggu_pembayaran')
                ->where('ongkir', '<=', 0)
                ->count();
            $needsStatusUpdateCount = \App\Models\Transaction::query()
                ->whereIn('status', ['dibayar', 'diproses', 'dikirim'])
                ->count();
            $needsWarrantyCount = \App\Models\Transaction::query()
                ->where('status', 'diterima')
                ->where('warranty_status', 'diajukan')
                ->count();
            $adminActionCount = $needsOngkirCount + $needsStatusUpdateCount + $needsWarrantyCount;
        }
    @endphp

    @if ($popupNotification && $notificationIndexUrl)
        @php
            $payload = $popupNotification->data;
            $popupTitle = $payload['title'] ?? 'Notifikasi';
            $popupMessage = $payload['message'] ?? '-';

            if ($user->role === 'admin') {
                $popupTitle = 'Notifikasi Transaksi';
                $popupMessage = $adminActionCount > 0
                    ? "Ada {$adminActionCount} transaksi yang perlu ditindaklanjuti. {$popupMessage}"
                    : $popupMessage;
            }
        @endphp

        <div id="notification-popup"
            class="pointer-events-none fixed bottom-5 right-5 z-[80] w-[calc(100%-2.5rem)] max-w-sm translate-y-3 overflow-hidden rounded-lg border border-border-strong/80 bg-white/85 opacity-0 shadow-2xl shadow-black/15 backdrop-blur-md transition duration-300 sm:bottom-6 sm:right-6"
            data-auto-dismiss-ms="5000"
            data-storage-key="siracas:notification-popup-shown:{{ $user->role }}:{{ $user->id }}">
            <div class="flex items-start gap-4 p-4">
                <div
                    class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-soft/80 text-primary-dark">
                    <x-icons.bell class="h-5 w-5" />
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-muted-light">
                        {{ str_replace('_', ' ', $payload['type'] ?? 'notifikasi') }}
                    </p>
                    <h2 class="mt-1 text-sm font-black text-text-body">{{ $popupTitle }}</h2>
                    <p class="mt-1 line-clamp-3 text-sm leading-6 text-muted">{{ $popupMessage }}</p>

                    <a href="{{ $notificationIndexUrl }}"
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

                const storageKey = popup.dataset.storageKey;

                try {
                    if (window.sessionStorage.getItem(storageKey) === '1') {
                        popup.remove();
                        return;
                    }

                    window.sessionStorage.setItem(storageKey, '1');
                } catch (error) {
                    // If browser storage is unavailable, keep the popup behavior usable.
                }

                requestAnimationFrame(() => {
                    popup.classList.remove('pointer-events-none', 'translate-y-3', 'opacity-0');
                });

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
@endauth
