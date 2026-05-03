@php
    $isEdit = $mode === 'edit';
@endphp

<div class="space-y-5" x-data="{ preview: null }">
    <div class="flex flex-col items-center gap-4">

        <!-- FOTO -->
        <div
            class="flex h-40 w-40 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
            <template x-if="preview">
                <img :src="preview" class="h-full w-full object-cover">
            </template>

            @if ($isEdit)
                <template x-if="!preview && selected">
                    <img :src="selected.foto_url" class="h-full w-full object-cover">
                </template>
            @else
                <template x-if="!preview">
                    <img src="{{ asset('images/logo.png') }}" class="h-full w-full object-cover opacity-60">
                </template>
            @endif
        </div>

        <!-- BUTTON -->
        <div class="text-center">
            <label for="{{ $mode }}_foto"
                class="inline-flex h-10 cursor-pointer items-center rounded-lg bg-[#9e836f] px-5 text-sm font-semibold text-white hover:bg-[#8a725f]">
                Unggah Foto
            </label>

            <input id="{{ $mode }}_foto" type="file" name="foto" class="hidden"
                @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">

            <p class="mt-2 text-xs text-gray-500">
                JPG, PNG, atau WEBP. Maksimal 2 MB.
            </p>
        </div>

    </div>

    <div>
        <label class="siracas-label text-black">Nama Produk</label>
        <input type="text" name="nama_produk" maxlength="64" value="{{ $isEdit ? '' : old('nama_produk') }}"
            @if ($isEdit) x-model="selected.nama_produk" @endif class="siracas-input siracas-input-control bg-white">
        @error('nama_produk')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label class="siracas-label text-black">Harga</label>
            <input type="number" name="harga" min="0" step="1" value="{{ $isEdit ? '' : old('harga') }}"
                @if ($isEdit) x-model="selected.harga" @endif class="siracas-input siracas-input-control bg-white">
            @error('harga')
                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="siracas-label text-black">Stok</label>
            <input type="number" name="stok" min="0" step="1" value="{{ $isEdit ? '' : old('stok') }}"
                @if ($isEdit) x-model="selected.stok" @endif class="siracas-input siracas-input-control bg-white">
            @error('stok')
                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="siracas-label text-black">Satuan</label>
            <select name="satuan" @if ($isEdit) x-model="selected.satuan" @endif
                class="siracas-input siracas-input-control bg-white">
                <option value="">Pilih</option>
                @foreach (App\Models\Product::SATUAN as $value => $label)
                    <option value="{{ $value }}" @selected(!$isEdit && old('satuan') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('satuan')
                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label class="siracas-label text-black">Deskripsi</label>
        <textarea name="deskripsi" rows="4" @if ($isEdit) x-model="selected.deskripsi" @endif
            class="siracas-input siracas-textarea bg-white">{{ $isEdit ? '' : old('deskripsi') }}</textarea>
        @error('deskripsi')
            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-3 pt-2 sm:flex sm:justify-end">
        <x-ui.button type="submit">
            Simpan
        </x-ui.button>
        <x-ui.button type="button" variant="ghost" @click="closeModal()">
            Batal
        </x-ui.button>
    </div>
</div>
