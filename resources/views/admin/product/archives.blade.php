<x-layouts.admin title="Arsip Produk">
    <x-slot:actions>
        <form action="{{ route('admin.product.archives') }}" method="GET" class="relative w-full sm:w-[430px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk yang dihapus"
                class="h-12 w-full rounded-full border-0 bg-white px-6 pr-14 text-sm text-gray-700 shadow-sm outline-none placeholder:text-gray-500 focus:ring-2 focus:ring-[#d5c6ba] sm:px-8 sm:text-base">
            <button type="submit"
                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-[#7d6758]"
                aria-label="Cari produk arsip">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                </svg>
            </button>
        </form>
    </x-slot:actions>

    @if (session('success'))
        <div class="mb-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-black">Produk Dihapus</h2>
            <p class="mt-1 text-sm text-gray-500">Lihat produk yang sudah dihapus dan kembalikan jika dibutuhkan.</p>
        </div>

        <a href="{{ route('admin.product.index') }}"
            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-[#d5c6ba] bg-white px-5 text-sm font-semibold text-[#7d6758] shadow-sm transition hover:bg-[#f6f1ee] focus:outline-none focus:ring-2 focus:ring-[#d5c6ba]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" />
            </svg>
            Kembali ke Produk
        </a>
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
                        <th class="px-6 py-5 font-semibold">Dihapus Pada</th>
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
                                        <p class="truncate font-semibold text-[#3f3935]">{{ $product->nama_produk }}</p>
                                        <p class="line-clamp-1 text-xs text-gray-500">
                                            {{ $product->deskripsi ?: 'Belum ada deskripsi' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">Rp{{ number_format((float) $product->harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $product->stok }} {{ strtoupper($product->satuan) }}</td>
                            <td class="px-6 py-4">
                                {{ $product->deleted_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.product.restore', $product->id) }}" method="POST"
                                    class="flex justify-center">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-[#59815f] px-4 text-sm font-semibold text-white transition hover:bg-[#4f7355]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                        </svg>
                                        Pulihkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada produk yang dihapus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="border-t border-gray-100 px-4 py-6">
                {{ $products->links() }}
            </div>
        @endif
    </section>
</x-layouts.admin>
