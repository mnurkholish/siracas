@php
    $navLinks = [
        ['nav' => 'Beranda', 'route' => route('customer.dashboard')],
        ['nav' => 'Produk', 'route' => route('customer.product.index')],
        ['nav' => 'Keranjang', 'route' => route('cart.index')],
        ['nav' => 'Transaksi', 'route' => route('transactions.index')],
    ];

    $oldProvinsiId = old('provinsi_id');
    $oldKotaId = old('kota_id');
    $oldKecamatanId = old('kecamatan_id');
@endphp

<x-layouts.public title="Buat Alamat - SIRACAS">
    <x-home.navbar :nav-links="$navLinks" />

    <main class="bg-[#f7f2ed] px-6 py-10 lg:px-10">
        <section class="mx-auto max-w-3xl">
            <a href="{{ $returnTo }}" class="text-sm font-semibold text-[#8c725f] transition hover:text-[#6f5a4c]">
                Kembali
            </a>

            <div class="mt-4 rounded-lg border border-[#e2d6cc] bg-white p-6 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-[0.35em] text-[#b7a69a]">Alamat</p>
                <h1 class="mt-2 text-3xl font-black text-[#6f5a4c]">Buat Alamat Baru</h1>

                @if ($errors->any())
                    <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('addresses.store') }}" method="POST"
                    x-data="addressForm({
                        oldProvinsiId: @js($oldProvinsiId),
                        oldKotaId: @js($oldKotaId),
                        oldKecamatanId: @js($oldKecamatanId),
                        kotasUrl: @js(route('addresses.wilayah.kotas')),
                        kecamatansUrl: @js(route('addresses.wilayah.kecamatans')),
                    })"
                    x-init="init()"
                    class="mt-6 space-y-5">
                    @csrf
                    <input type="hidden" name="return_to" value="{{ old('return_to', $returnTo) }}">

                    <div>
                        <label for="provinsi" class="mb-2 block text-sm font-bold text-[#5f4f45]">Provinsi</label>
                        <select id="provinsi" name="provinsi_id" x-model="provinsiId" @change="onProvinsiChange"
                            class="h-12 w-full rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 text-sm text-[#5d5048] outline-none transition focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]">
                            <option value="">Pilih provinsi</option>
                            @foreach ($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}" @selected((string) $oldProvinsiId === (string) $provinsi->id)>
                                    {{ $provinsi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="kota" class="mb-2 block text-sm font-bold text-[#5f4f45]">Kota</label>
                        <select id="kota" name="kota_id" x-model="kotaId" @change="onKotaChange" :disabled="!provinsiId || loadingKotas"
                            class="h-12 w-full rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 text-sm text-[#5d5048] outline-none transition focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7] disabled:cursor-not-allowed disabled:bg-[#eee4dc]">
                            <option value="" x-text="loadingKotas ? 'Memuat kota...' : 'Pilih kota'"></option>
                            <template x-for="kota in kotas" :key="kota.id">
                                <option :value="kota.id" x-text="kota.nama"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="kecamatan_id" class="mb-2 block text-sm font-bold text-[#5f4f45]">Kecamatan</label>
                        <select id="kecamatan_id" name="kecamatan_id" x-model="kecamatanId" :disabled="!kotaId || loadingKecamatans"
                            class="h-12 w-full rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 text-sm text-[#5d5048] outline-none transition focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7] disabled:cursor-not-allowed disabled:bg-[#eee4dc]">
                            <option value="" x-text="loadingKecamatans ? 'Memuat kecamatan...' : 'Pilih kecamatan'"></option>
                            <template x-for="kecamatan in kecamatans" :key="kecamatan.id">
                                <option :value="kecamatan.id" x-text="kecamatan.nama"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="detail_alamat" class="mb-2 block text-sm font-bold text-[#5f4f45]">Detail Alamat</label>
                        <textarea id="detail_alamat" name="detail_alamat" rows="5"
                            class="w-full rounded-lg border border-[#e1d5cb] bg-[#fbf8f5] px-4 py-3 text-sm text-[#5d5048] outline-none transition focus:border-[#a6866d] focus:bg-white focus:ring-2 focus:ring-[#eadfd7]"
                            placeholder="Nama jalan, nomor rumah, RT/RW, patokan">{{ old('detail_alamat') }}</textarea>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button type="submit"
                            class="inline-flex h-11 items-center justify-center rounded-lg bg-[#9e836f] px-6 text-sm font-bold text-white transition hover:bg-[#8a725f]">
                            Simpan Alamat
                        </button>
                        <a href="{{ $returnTo }}"
                            class="inline-flex h-11 items-center justify-center rounded-lg border border-[#d8c9bc] bg-white px-6 text-sm font-bold text-[#6f5a4c] transition hover:bg-[#f7f1eb]">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <script>
        function addressForm(config) {
            return {
                provinsiId: config.oldProvinsiId || '',
                kotaId: config.oldKotaId || '',
                kecamatanId: config.oldKecamatanId || '',
                kotas: [],
                kecamatans: [],
                loadingKotas: false,
                loadingKecamatans: false,
                async init() {
                    if (this.provinsiId) {
                        await this.loadKotas();
                    }

                    if (this.kotaId) {
                        await this.loadKecamatans();
                    }
                },
                async onProvinsiChange() {
                    this.kotaId = '';
                    this.kecamatanId = '';
                    this.kotas = [];
                    this.kecamatans = [];

                    if (this.provinsiId) {
                        await this.loadKotas();
                    }
                },
                async onKotaChange() {
                    this.kecamatanId = '';
                    this.kecamatans = [];

                    if (this.kotaId) {
                        await this.loadKecamatans();
                    }
                },
                async loadKotas() {
                    this.loadingKotas = true;

                    try {
                        const url = new URL(config.kotasUrl);
                        url.searchParams.set('provinsi_id', this.provinsiId);
                        const response = await fetch(url);
                        this.kotas = response.ok ? await response.json() : [];
                    } finally {
                        this.loadingKotas = false;
                    }
                },
                async loadKecamatans() {
                    this.loadingKecamatans = true;

                    try {
                        const url = new URL(config.kecamatansUrl);
                        url.searchParams.set('kota_id', this.kotaId);
                        const response = await fetch(url);
                        this.kecamatans = response.ok ? await response.json() : [];
                    } finally {
                        this.loadingKecamatans = false;
                    }
                }
            };
        }
    </script>

    <x-home.footer />
</x-layouts.public>
