@php
    $oldContext = old('form_context');
    $oldCampaignId = old('campaign_id');
    $campaignRows = $campaigns
        ->getCollection()
        ->map(fn ($campaign) => [
            'id' => $campaign->id,
            'type' => $campaign->type,
            'title' => $campaign->title,
            'message' => $campaign->message,
            'url' => $campaign->url,
            'is_active' => $campaign->is_active,
            'update_url' => route('admin.notification-campaigns.update', $campaign),
        ])
        ->values();
@endphp

<x-layouts.admin title="Campaign Notifikasi" subtitle="Kelola promo, produk baru, dan pengumuman in-app untuk customer.">
    <x-slot:actions>
        <form action="{{ route('admin.notification-campaigns.index') }}" method="GET" class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari campaign"
                class="h-11 w-full rounded-lg border border-border-strong bg-white px-4 text-sm outline-none focus:ring-2 focus:ring-border-soft sm:w-72">
            <select name="type"
                class="h-11 rounded-lg border border-border-strong bg-white px-4 text-sm outline-none focus:ring-2 focus:ring-border-soft">
                <option value="">Semua tipe</option>
                @foreach ($types as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-button type="submit" size="lg">Cari</x-button>
        </form>
    </x-slot:actions>

    <div x-data="notificationCampaignAdmin({
        campaigns: @js($campaignRows),
        activeModal: @js($oldContext ?: ''),
        oldCampaignId: @js($oldCampaignId),
        oldValues: @js(old()),
    })" x-init="init()" @keydown.escape.window="closeModal()">
        @if ($errors->any())
            <div class="alert-danger mb-5">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-black">Daftar Campaign</h2>
                <p class="mt-1 text-sm text-gray-500">Campaign admin bisa dipublish ke seluruh customer aktif.</p>
            </div>
            <x-button type="button" @click="openCreate()" size="lg">
                Tambah Campaign
            </x-button>
        </div>

        <section class="table-wrap">
            <div class="overflow-x-auto">
                <table class="admin-table min-w-[980px]">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($campaigns as $campaign)
                            <tr>
                                <td>
                                    <p class="font-semibold text-muted-dark">{{ $campaign->title }}</p>
                                    <p class="mt-1 line-clamp-1 text-xs text-gray-500">{{ $campaign->message }}</p>
                                    @if ($campaign->url)
                                        <a href="{{ $campaign->url }}" target="_blank" rel="noopener"
                                            class="mt-1 inline-flex text-xs font-semibold text-primary-dark">Link campaign</a>
                                    @endif
                                </td>
                                <td>{{ $campaign->typeLabel() }}</td>
                                <td>
                                    <x-badge :variant="$campaign->is_active ? 'success' : 'muted'">
                                        {{ $campaign->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </x-badge>
                                </td>
                                <td>{{ $campaign->published_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td>
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <x-button type="button" @click="openEdit({{ $campaign->id }})" variant="secondary" size="sm">
                                            Ubah
                                        </x-button>

                                        <form action="{{ route('admin.notification-campaigns.publish', $campaign) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <x-button type="submit" size="sm">Publish</x-button>
                                        </form>

                                        <form action="{{ route('admin.notification-campaigns.unpublish', $campaign) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <x-button type="submit" variant="secondary" size="sm">Unpublish</x-button>
                                        </form>

                                        <form action="{{ route('admin.notification-campaigns.destroy', $campaign) }}" method="POST" class="delete-campaign-form">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" variant="danger-soft" size="sm">Hapus</x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">
                                    Belum ada campaign notifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($campaigns->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </section>

        <div x-show="activeModal === 'create'" x-cloak class="modal-backdrop" x-transition.opacity>
            <div @click.outside="closeModal()" class="modal-panel max-w-2xl">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <h2 class="text-lg font-bold text-black">Tambah Campaign</h2>
                    <button type="button" @click="closeModal()" class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <form action="{{ route('admin.notification-campaigns.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="form_context" value="create">
                    @include('admin.notification-campaigns.partials.form-fields', ['types' => $types, 'mode' => 'create'])
                </form>
            </div>
        </div>

        <div x-show="activeModal === 'edit'" x-cloak class="modal-backdrop" x-transition.opacity>
            <div @click.outside="closeModal()" class="modal-panel max-w-2xl">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <h2 class="text-lg font-bold text-black">Ubah Campaign</h2>
                    <button type="button" @click="closeModal()" class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <template x-if="selected">
                    <form :action="selected.update_url" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit">
                        <input type="hidden" name="campaign_id" :value="selected.id">
                        @include('admin.notification-campaigns.partials.form-fields', ['types' => $types, 'mode' => 'edit'])
                    </form>
                </template>
            </div>
        </div>
    </div>

    <script>
        function notificationCampaignAdmin(config) {
            return {
                campaigns: config.campaigns,
                activeModal: config.activeModal,
                selected: null,
                oldValues: config.oldValues || {},
                init() {
                    if (this.activeModal === 'edit' && config.oldCampaignId) {
                        this.openEdit(Number(config.oldCampaignId));
                        Object.assign(this.selected, {
                            type: this.oldValues.type ?? this.selected.type,
                            title: this.oldValues.title ?? this.selected.title,
                            message: this.oldValues.message ?? this.selected.message,
                            url: this.oldValues.url ?? this.selected.url,
                            is_active: Boolean(Number(this.oldValues.is_active ?? this.selected.is_active)),
                        });
                    }
                },
                openCreate() {
                    this.selected = null;
                    this.activeModal = 'create';
                },
                openEdit(id) {
                    this.selected = {
                        ...this.campaigns.find((campaign) => campaign.id === id)
                    };
                    this.activeModal = 'edit';
                },
                closeModal() {
                    this.activeModal = '';
                },
            };
        }

        document.querySelectorAll('.delete-campaign-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus campaign?',
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
</x-layouts.admin>
