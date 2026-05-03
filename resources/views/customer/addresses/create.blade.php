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

    <main class="page">
        <section class="mx-auto max-w-3xl">
            <a href="{{ $returnTo }}" class="text-sm font-semibold text-primary-dark transition hover:text-text-body">
                Kembali
            </a>

            <div class="card mt-4 p-6">
                <p class="eyebrow">Alamat</p>
                <h1 class="page-title">Buat Alamat Baru</h1>

                @if ($errors->any())
                    <div class="alert-danger mt-6">
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
                        <label for="provinsi" class="form-label">Provinsi</label>
                        <select id="provinsi" name="provinsi_id" x-model="provinsiId" @change="onProvinsiChange"
                            class="form-control input-control">
                            <option value="">Pilih provinsi</option>
                            @foreach ($provinsis as $provinsi)
                                <option value="{{ $provinsi->id }}" @selected((string) $oldProvinsiId === (string) $provinsi->id)>
                                    {{ $provinsi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="kota" class="form-label">Kota</label>
                        <select id="kota" name="kota_id" x-model="kotaId" @change="onKotaChange" :disabled="!provinsiId || loadingKotas"
                            class="form-control input-control">
                            <option value="" x-text="loadingKotas ? 'Memuat kota...' : 'Pilih kota'"></option>
                            <template x-for="kota in kotas" :key="kota.id">
                                <option :value="kota.id" x-text="kota.nama"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="kecamatan_id" class="form-label">Kecamatan</label>
                        <select id="kecamatan_id" name="kecamatan_id" x-model="kecamatanId" :disabled="!kotaId || loadingKecamatans"
                            class="form-control input-control">
                            <option value="" x-text="loadingKecamatans ? 'Memuat kecamatan...' : 'Pilih kecamatan'"></option>
                            <template x-for="kecamatan in kecamatans" :key="kecamatan.id">
                                <option :value="kecamatan.id" x-text="kecamatan.nama"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="detail_alamat" class="form-label">Detail Alamat</label>
                        <textarea id="detail_alamat" name="detail_alamat" rows="5"
                            class="form-control textarea-control"
                            placeholder="Nama jalan, nomor rumah, RT/RW, patokan">{{ old('detail_alamat') }}</textarea>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <x-button type="submit" size="lg">
                            Simpan Alamat
                        </x-button>
                        <x-button :href="$returnTo" variant="secondary" size="lg">
                            Batal
                        </x-button>
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
