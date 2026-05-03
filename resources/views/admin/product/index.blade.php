@php
    $oldContext = old('form_context');
    $oldProductId = old('product_id');
    $productRows = $products
        ->getCollection()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'nama_produk' => $product->nama_produk,
                'harga' => (float) $product->harga,
                'stok' => $product->stok,
                'satuan' => $product->satuan,
                'deskripsi' => $product->deskripsi,
                'foto_url' => $product->foto ? asset('storage/' . $product->foto) : asset('images/logo.png'),
                'show_url' => route('admin.product.show', $product),
                'update_url' => route('admin.product.update', $product),
                'delete_url' => route('admin.product.destroy', $product),
            ];
        })
        ->values();
@endphp

<x-layouts.admin title="Produk">
    <x-slot:actions>
        <form action="{{ route('admin.product.index') }}" method="GET" class="relative w-full sm:w-[430px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk berdasarkan nama"
                class="h-12 w-full rounded-full border-0 bg-white px-6 pr-14 text-sm text-gray-700 shadow-sm outline-none placeholder:text-gray-500 focus:ring-2 focus:ring-[#d5c6ba] sm:px-8 sm:text-base">
            <button type="submit"
                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-[#7d6758]"
                aria-label="Cari produk">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                </svg>
            </button>
        </form>
    </x-slot:actions>

    <div x-data="productAdmin({
        products: @js($productRows),
        activeModal: @js($oldContext ?: ''),
        oldProductId: @js($oldProductId),
        oldValues: @js(old()),
    })" x-init="init()" @keydown.escape.window="closeModal()">
        @if ($errors->any())
            <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-black">Daftar Produk</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola produk yang tampil dan dipakai dalam transaksi SIRACAS.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <a href="{{ route('admin.product.archives') }}"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-[#d5c6ba] bg-white px-5 text-sm font-semibold text-[#7d6758] shadow-sm transition hover:bg-[#f6f1ee] focus:outline-none focus:ring-2 focus:ring-[#d5c6ba]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6l4 2m5-2a9 9 0 1 1-3.65-7.24M21 3v6h-6" />
                    </svg>
                    Arsip Produk
                </a>
                <button type="button" @click="openCreate()"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-[#9e836f] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#8a725f] focus:outline-none focus:ring-2 focus:ring-[#c9b5a7]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                    </svg>
                    Tambah Produk
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-md bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] border-collapse text-left text-black">
                    <thead>
                        <tr class="bg-[#e3d9d1] text-sm">
                            <th class="w-20 px-6 py-5 text-center font-semibold">No.</th>
                            <th class="px-6 py-5 font-semibold">Produk</th>
                            <th class="px-6 py-5 font-semibold">Harga</th>
                            <th class="px-6 py-5 font-semibold">Stok</th>
                            <th class="px-6 py-5 font-semibold">Satuan</th>
                            <th class="px-6 py-5 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse ($products as $product)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4 text-center">
                                    {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product->foto ? asset('storage/' . $product->foto) : asset('images/logo.png') }}"
                                            alt="Foto {{ $product->nama_produk }}"
                                            class="h-12 w-12 rounded-md border border-gray-200 object-cover">
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-[#3f3935]">{{ $product->nama_produk }}
                                            </p>
                                            <p class="line-clamp-1 text-xs text-gray-500">
                                                {{ $product->deskripsi ?: 'Belum ada deskripsi' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">Rp{{ number_format((float) $product->harga, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">{{ $product->stok }}</td>
                                <td class="px-6 py-4">{{ strtoupper($product->satuan) }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" @click="openDetail({{ $product->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[#5b5cff] transition hover:bg-[#f0f0ff]"
                                            aria-label="Lihat {{ $product->nama_produk }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="openEdit({{ $product->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[#d49a00] transition hover:bg-[#fff8df]"
                                            aria-label="Ubah {{ $product->nama_produk }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 7.125 16.875 4.5M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="openDelete({{ $product->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[#ef3f46] transition hover:bg-red-50"
                                            aria-label="Hapus {{ $product->nama_produk }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .563c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Tidak ada data produk ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                @php
                    $currentPage = $products->currentPage();
                    $lastPage = $products->lastPage();
                    $separatorShown = false;
                @endphp

                <div class="grid gap-4 border-t border-gray-100 px-4 py-6 lg:grid-cols-[1fr_auto_1fr] lg:items-center">
                    <div class="flex justify-center lg:justify-start">
                        @if ($products->onFirstPage())
                            <span
                                class="inline-flex h-11 cursor-not-allowed items-center gap-3 rounded-lg bg-white px-4 text-sm text-gray-400 shadow-sm ring-1 ring-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 12H5m0 0 6 6m-6-6 6-6" />
                                </svg>
                                Sebelumnya
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}"
                                class="inline-flex h-11 items-center gap-3 rounded-lg bg-white px-4 text-sm text-black shadow-sm ring-1 ring-gray-100 transition hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 12H5m0 0 6 6m-6-6 6-6" />
                                </svg>
                                Sebelumnya
                            </a>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @for ($page = 1; $page <= $lastPage; $page++)
                            @php
                                $showPage =
                                    $page === 1 ||
                                    $page === $lastPage ||
                                    abs($page - $currentPage) <= 1 ||
                                    ($currentPage <= 3 && $page <= 5) ||
                                    ($currentPage >= $lastPage - 2 && $page >= $lastPage - 4);
                            @endphp

                            @if ($showPage)
                                @php $separatorShown = false; @endphp
                                @if ($page === $currentPage)
                                    <span
                                        class="inline-flex h-9 min-w-9 items-center justify-center rounded bg-[#e3d9d1] px-3 text-sm font-semibold text-black">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $products->url($page) }}"
                                        class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-300 bg-white px-3 text-sm text-black transition hover:border-[#a98c77] hover:bg-[#f6f1ee]">
                                        {{ $page }}
                                    </a>
                                @endif
                            @elseif (!$separatorShown)
                                @php $separatorShown = true; @endphp
                                <span
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-300 bg-white px-3 text-sm font-semibold text-black">
                                    .....
                                </span>
                            @endif
                        @endfor
                    </div>

                    <div class="flex justify-center lg:justify-end">
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}"
                                class="inline-flex h-11 items-center gap-3 rounded-lg bg-white px-4 text-sm text-black shadow-sm ring-1 ring-gray-100 transition hover:bg-gray-50">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14m0 0-6-6m6 6-6 6" />
                                </svg>
                            </a>
                        @else
                            <span
                                class="inline-flex h-11 cursor-not-allowed items-center gap-3 rounded-lg bg-white px-4 text-sm text-gray-400 shadow-sm ring-1 ring-gray-100">
                                Selanjutnya
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M5 12h14m0 0-6-6m6 6-6 6" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <div x-show="activeModal === 'create'" x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/35 px-4 py-8"
            x-transition.opacity>
            <div @click.outside="closeModal()"
                class="max-h-[calc(100vh-3rem)] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-5 shadow-2xl sm:p-8">
                <h2 class="mb-6 text-lg font-bold text-black">Tambah Produk</h2>
                <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form_context" value="create">
                    @include('admin.product.partials.form-fields', ['mode' => 'create'])
                </form>
            </div>
        </div>

        <div x-show="activeModal === 'detail'" x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/35 px-4 py-8"
            x-transition.opacity>
            <div @click.outside="closeModal()"
                class="max-h-[calc(100vh-3rem)] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-5 shadow-2xl sm:p-8">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <h2 class="text-lg font-bold text-black">Detail Produk</h2>
                    <button type="button" @click="closeModal()"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                        aria-label="Tutup modal">&times;</button>
                </div>

                <template x-if="selected">
                    <div>
                        <!-- FOTO + NAMA -->
                        <div class="mb-6 flex flex-col items-center text-center">
                            <img :src="selected.foto_url"
                                class="h-48 w-48 rounded-lg border border-gray-200 object-cover">

                            <h3 class="mt-4 text-xl font-bold text-[#3f3935]" x-text="selected.nama_produk"></h3>
                        </div>

                        <!-- STOK & SATUAN -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-md border border-gray-100 bg-gray-50 px-4 py-3 text-center">
                                <p class="text-xs font-semibold uppercase text-gray-400">Stok</p>
                                <p class="mt-1 font-bold text-black" x-text="selected.stok"></p>
                            </div>

                            <div class="rounded-md border border-gray-100 bg-gray-50 px-4 py-3 text-center">
                                <p class="text-xs font-semibold uppercase text-gray-400">Satuan</p>
                                <p class="mt-1 font-bold text-black" x-text="selected.satuan.toUpperCase()"></p>
                            </div>
                        </div>

                        <!-- HARGA (FULL WIDTH) -->
                        <div class="mt-4 rounded-md border border-gray-100 bg-[#f6f1ee] px-4 py-4 text-center">
                            <p class="text-xs font-semibold uppercase text-gray-500">Harga</p>
                            <p class="mt-1 text-lg font-bold text-[#9e836f]" x-text="currency(selected.harga)"></p>
                        </div>

                        <!-- DESKRIPSI -->
                        <div class="mt-5">
                            <p class="mb-2 text-sm font-bold text-black">Deskripsi</p>
                            <p class="rounded-md border border-gray-100 bg-gray-50 px-4 py-3 text-sm leading-6 text-gray-600"
                                x-text="selected.deskripsi || 'Belum ada deskripsi'"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="activeModal === 'edit'" x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/35 px-4 py-8"
            x-transition.opacity>
            <div @click.outside="closeModal()"
                class="max-h-[calc(100vh-3rem)] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-5 shadow-2xl sm:p-8">
                <h2 class="mb-6 text-lg font-bold text-black">Ubah Produk</h2>
                <template x-if="selected">
                    <form :action="selected.update_url" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit">
                        <input type="hidden" name="product_id" :value="selected.id">
                        @include('admin.product.partials.form-fields', ['mode' => 'edit'])
                    </form>
                </template>
            </div>
        </div>

        <div x-show="activeModal === 'delete'" x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/35 px-4 py-8"
            x-transition.opacity>
            <div @click.outside="closeModal()" class="w-full max-w-md rounded-lg bg-white p-6 shadow-2xl">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-[#ef3f46]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-black">Hapus Produk</h2>
                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Apakah anda yakin untuk menghapus produk ini?
                </p>

                <template x-if="selected">
                    <form :action="selected.delete_url" method="POST"
                        class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="h-10 rounded-lg bg-[#ef3f46] px-6 text-sm font-semibold text-white transition hover:bg-red-600">
                            Yakin
                        </button>
                        <button type="button" @click="closeModal()"
                            class="h-10 rounded-lg border border-gray-200 bg-white px-6 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Batal
                        </button>
                    </form>
                </template>
            </div>
        </div>
    </div>

    <script>
        function productAdmin(config) {
            return {
                products: config.products,
                activeModal: config.activeModal,
                selected: null,
                oldValues: config.oldValues || {},
                init() {
                    if (this.activeModal === 'edit' && config.oldProductId) {
                        this.openEdit(Number(config.oldProductId));
                        Object.assign(this.selected, {
                            nama_produk: this.oldValues.nama_produk ?? this.selected.nama_produk,
                            harga: this.oldValues.harga ?? this.selected.harga,
                            stok: this.oldValues.stok ?? this.selected.stok,
                            satuan: this.oldValues.satuan ?? this.selected.satuan,
                            deskripsi: this.oldValues.deskripsi ?? this.selected.deskripsi,
                        });
                    }
                },
                findProduct(id) {
                    return this.products.find((product) => product.id === id);
                },
                openCreate() {
                    this.selected = null;
                    this.activeModal = 'create';
                },
                openDetail(id) {
                    this.selected = {
                        ...this.findProduct(id)
                    };
                    this.activeModal = 'detail';
                },
                openEdit(id) {
                    this.selected = {
                        ...this.findProduct(id)
                    };
                    this.activeModal = 'edit';
                },
                openDelete(id) {
                    this.selected = {
                        ...this.findProduct(id)
                    };
                    this.activeModal = 'delete';
                },
                closeModal() {
                    this.activeModal = '';
                },
                currency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0,
                    }).format(value || 0);
                },
            };
        }
    </script>
</x-layouts.admin>
