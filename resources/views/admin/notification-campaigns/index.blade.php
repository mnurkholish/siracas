@php
    $oldContext = old('form_context');
    $oldCampaignId = old('campaign_id');
    $productRows = $products
        ->map(
            fn($product) => [
                'id' => $product->id,
                'name' => $product->nama_produk,
                'url' => route('products.show', $product, absolute: false),
            ],
        )
        ->values();
    $campaignRows = $campaigns
        ->getCollection()
        ->map(
            fn($campaign) => [
                'id' => $campaign->id,
                'type' => $campaign->type,
                'title' => $campaign->title,
                'message' => $campaign->message,
                'url' => $campaign->url,
                'image_url' => $campaign->image ? asset('storage/' . $campaign->image) : null,
                'update_url' => route('admin.campaigns.update', $campaign),
            ],
        )
        ->values();
@endphp

<x-layouts.admin title="Kampanye Notifikasi"
    subtitle="Kelola notifikasi promo, produk baru, dan pengumuman in-app untuk customer.">
    <x-slot:actions>
        <form action="{{ route('admin.campaigns.index') }}" method="GET"
            class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kampanye"
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
        products: @js($productRows),
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
                <h2 class="text-xl font-bold text-black">Daftar Kampanye Notofikasi</h2>
            </div>
            <x-button type="button" @click="openCreate()" size="lg">
                Tambah Kampanye
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
                            <th>Waktu Publish</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($campaigns as $campaign)
                            <tr>
                                <td data-label="Judul">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-muted-dark">{{ $campaign->title }}</p>
                                        <p class="mt-1 line-clamp-1 text-xs text-gray-500">{{ $campaign->message }}</p>

                                        @if ($campaign->image)
                                            <img src="{{ asset('storage/' . $campaign->image) }}"
                                                alt="Gambar {{ $campaign->title }}"
                                                class="mt-2 h-12 w-12 rounded-md border border-gray-200 object-cover">
                                        @endif

                                        @if ($campaign->url)
                                            <a href="{{ $campaign->url }}" target="_blank" rel="noopener"
                                                class="mt-1 inline-flex text-xs font-semibold text-primary-dark">Tautan
                                                kampanye</a>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Tipe">{{ $campaign->typeLabel() }}</td>
                                <td data-label="Status">
                                    <x-badge :variant="$campaign->is_active ? 'success' : 'muted'">
                                        {{ $campaign->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </x-badge>
                                </td>
                                <td data-label="Waktu Publish">{{ $campaign->published_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td data-label="Aksi">
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <x-button type="button" @click="openEdit({{ $campaign->id }})"
                                            variant="secondary" size="sm">
                                            Ubah
                                        </x-button>

                                        @if ($campaign->is_active)
                                            <form action="{{ route('admin.campaigns.unpublish', $campaign) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-button type="submit" variant="secondary"
                                                    size="sm">Unpublish</x-button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.campaigns.publish', $campaign) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-button type="submit" size="sm">Publish</x-button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST"
                                            class="delete-campaign-form">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" variant="danger-soft"
                                                size="sm">Hapus</x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">
                                    Belum ada kampanye notifikasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($campaigns->hasPages())
                <div class="border-t border-gray-100 px-4 py-6">
                    <x-pagination :paginator="$campaigns" />
                </div>
            @endif
        </section>

        <div x-show="activeModal === 'create'" x-cloak class="modal-backdrop" x-transition.opacity>
            <div @click.outside="closeModal()" class="modal-panel max-w-2xl">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <h2 class="text-lg font-bold text-black">Tambah Kampanye</h2>
                    <button type="button" @click="closeModal()"
                        class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    <input type="hidden" name="form_context" value="create">
                    @include('admin.notification-campaigns.partials.form-fields', [
                        'types' => $types,
                        'products' => $products,
                        'mode' => 'create',
                    ])
                </form>
            </div>
        </div>

        <div x-show="activeModal === 'edit'" x-cloak class="modal-backdrop" x-transition.opacity>
            <div @click.outside="closeModal()" class="modal-panel max-w-2xl">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <h2 class="text-lg font-bold text-black">Ubah Kampanye</h2>
                    <button type="button" @click="closeModal()"
                        class="text-2xl leading-none text-gray-400 hover:text-gray-700">&times;</button>
                </div>

                <template x-if="selected">
                    <form :action="selected.update_url" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit">
                        <input type="hidden" name="campaign_id" :value="selected.id">
                        @include('admin.notification-campaigns.partials.form-fields', [
                            'types' => $types,
                            'products' => $products,
                            'mode' => 'edit',
                        ])
                    </form>
                </template>
            </div>
        </div>
    </div>

    <script>
        function notificationCampaignAdmin(config) {
            return {
                campaigns: config.campaigns,
                products: config.products,
                activeModal: config.activeModal,
                selected: null,
                oldValues: config.oldValues || {},
                createForm: {
                    type: config.oldValues?.type || '',
                    product_id: Number(config.oldValues?.product_id || 0) || '',
                },
                init() {
                    if (this.activeModal === 'edit' && config.oldCampaignId) {
                        this.openEdit(Number(config.oldCampaignId));
                        Object.assign(this.selected, {
                            type: this.oldValues.type ?? this.selected.type,
                            product_id: Number(this.oldValues.product_id || 0) || this.selected.product_id,
                            title: this.oldValues.title ?? this.selected.title,
                            message: this.oldValues.message ?? this.selected.message,
                            url: this.oldValues.url ?? this.selected.url,
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
                    this.selected.product_id = this.productIdFromUrl(this.selected.url);
                    this.activeModal = 'edit';
                },
                currentType(mode) {
                    return mode === 'create' ? this.createForm.type : this.selected?.type;
                },
                productIdFromUrl(url) {
                    return this.products.find((product) => product.url === url)?.id || '';
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
                    title: 'Hapus kampanye?',
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
