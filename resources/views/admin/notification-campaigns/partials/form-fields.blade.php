<div>
    <label for="{{ $mode }}-type" class="form-label">Tipe</label>
    <select id="{{ $mode }}-type" name="type" class="form-control input-control"
        @if ($mode === 'create') x-model="createForm.type" @else x-model="selected.type" @endif
        required>
        <option value="">Pilih tipe</option>
        @foreach ($types as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>

<div>
    <label for="{{ $mode }}-title" class="form-label">Judul</label>
    <input id="{{ $mode }}-title" type="text" name="title" class="form-control input-control"
        value="{{ $mode === 'create' ? old('title') : '' }}"
        @if ($mode === 'edit') x-model="selected.title" @endif required>
</div>

<div>
    <label for="{{ $mode }}-message" class="form-label">Pesan</label>
    <textarea id="{{ $mode }}-message" name="message" class="form-control textarea-control"
        @if ($mode === 'edit') x-model="selected.message" @endif required>{{ $mode === 'create' ? old('message') : '' }}</textarea>
</div>

<div x-show="currentType('{{ $mode }}') === 'product'" x-cloak>
    <label for="{{ $mode }}-product-id" class="form-label">Produk</label>
    <select id="{{ $mode }}-product-id" name="product_id" class="form-control input-control"
        @if ($mode === 'create') x-model="createForm.product_id" @else x-model="selected.product_id" @endif
        :required="currentType('{{ $mode }}') === 'product'">
        <option value="">Pilih produk</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->nama_produk }}</option>
        @endforeach
    </select>
</div>

<div x-show="currentType('{{ $mode }}') !== 'product'">
    <label for="{{ $mode }}-url" class="form-label">URL</label>
    <input id="{{ $mode }}-url" type="text" name="url" class="form-control input-control"
        value="{{ $mode === 'create' ? old('url') : '' }}"
        @if ($mode === 'edit') x-model="selected.url" @endif placeholder="https://example.com">
</div>

<div>
    <label for="{{ $mode }}-image" class="form-label">Gambar</label>
    @if ($mode === 'edit')
        <template x-if="selected.image_url">
            <div class="mb-3 flex items-center gap-3 rounded-lg border border-border-soft bg-surface px-3 py-3">
                <img :src="selected.image_url" alt="Gambar campaign saat ini"
                    class="h-12 w-12 rounded-md border border-gray-200 object-cover">
                <span class="text-sm font-semibold text-muted-dark">Gambar saat ini</span>
            </div>
        </template>
    @endif
    <input id="{{ $mode }}-image" type="file" name="image" accept="image/*"
        class="block w-full rounded-lg border border-border-strong bg-surface px-4 py-3 text-sm text-muted-dark file:mr-4 file:rounded-md file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white">
</div>

<div class="flex justify-end gap-3 pt-2">
    <x-button type="button" variant="secondary" @click="closeModal()">Batal</x-button>
    <x-button type="submit">Simpan</x-button>
</div>
