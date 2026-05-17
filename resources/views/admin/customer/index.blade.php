<x-layouts.admin title="Data Akun">
    <x-slot:actions>
        <form action="{{ route('admin.customer.index') }}" method="GET" class="relative w-full sm:w-[410px]">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari berdasarkan nama atau id"
                class="h-12 w-full rounded-full border-0 bg-white px-6 pr-14 text-sm text-gray-700 shadow-sm outline-none placeholder:text-gray-500 focus:ring-2 focus:ring-border-strong sm:px-8 sm:text-base">
            <button type="submit"
                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500 transition hover:text-primary-dark"
                aria-label="Cari customer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                </svg>
            </button>
        </form>
    </x-slot:actions>

        <section class="table-wrap">
            <div class="flex flex-col gap-4 border-b border-gray-100 bg-white px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
                @php
                    $baseFilter = array_filter(['search' => request('search')]);
                    $allUrl = $baseFilter === []
                        ? route('admin.customer.index')
                        : route('admin.customer.index') . '?' . http_build_query($baseFilter);
                @endphp

                <div class="flex flex-wrap gap-2">
                    <a href="{{ $allUrl }}"
                        class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold transition {{ blank(request('status')) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-soft hover:text-primary-dark' }}">
                        Semua
                    </a>
                    @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)
                        @php
                            $query = array_filter([
                                'search' => request('search'),
                                'status' => $value,
                            ]);
                        @endphp
                        <a href="{{ route('admin.customer.index') . '?' . http_build_query($query) }}"
                            class="inline-flex h-9 items-center rounded-full px-3 text-xs font-bold transition {{ request('status') === $value ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-primary-soft hover:text-primary-dark' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.customer.index') }}"
                        class="inline-flex h-10 items-center justify-center rounded-lg border border-border-strong bg-white px-4 text-sm font-semibold text-muted-dark transition hover:bg-primary-soft">
                        Reset
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table min-w-[900px] text-center">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Foto Profil</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customer->username }}</td>
                                <td>
                                    <div class="flex justify-center">
                                        <img src="{{ $customer->foto_profil ? asset('storage/' . $customer->foto_profil) : asset('images/default-avatar.png') }}"
                                            alt="Foto profil {{ $customer->username }}"
                                            class="h-8 w-8 rounded-full object-cover">
                                    </div>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $customer->is_active ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="openModal({{ $customer->id }})"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full text-indigo-600 transition hover:bg-indigo-50 hover:text-indigo-700"
                                            aria-label="Lihat detail {{ $customer->username }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.customer.status', array_merge([$customer], request()->query())) }}"
                                            method="POST" class="customer-status-form"
                                            data-action="{{ $customer->is_active ? 'nonaktifkan' : 'aktifkan' }}"
                                            data-username="{{ $customer->username }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $customer->is_active ? 'nonaktif' : 'aktif' }}">
                                            <button type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-full {{ $customer->is_active ? 'text-danger hover:bg-red-50' : 'text-success hover:bg-success-soft' }} transition"
                                                aria-label="{{ $customer->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $customer->username }}">
                                                @if ($customer->is_active)
                                                    <i class="bi bi-person-x text-lg"></i>
                                                @else
                                                    <i class="bi bi-person-check text-lg"></i>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">
                                    Tidak ada data akun customer ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @php
                $currentPage = $customers->currentPage();
                $lastPage = $customers->lastPage();
                $separatorShown = false;
            @endphp

            <div class="grid gap-4 border-t border-gray-100 px-4 py-6 lg:grid-cols-[1fr_auto_1fr] lg:items-center">
                <div class="flex justify-center lg:justify-start">
                    @if ($customers->onFirstPage())
                        <span
                            class="inline-flex h-11 cursor-not-allowed items-center gap-3 rounded-lg bg-white px-4 text-base text-gray-400 shadow-sm ring-1 ring-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" />
                            </svg>
                            Sebelumnya
                        </span>
                    @else
                        <a href="{{ $customers->previousPageUrl() }}"
                            class="inline-flex h-11 items-center gap-3 rounded-lg bg-white px-4 text-base text-black shadow-sm ring-1 ring-gray-100 transition hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" />
                            </svg>
                            Sebelumnya
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
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
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded bg-secondary px-3 text-base font-semibold text-black">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $customers->url($page) }}"
                                    class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-300 bg-white px-3 text-base text-black transition hover:border-primary hover:bg-primary-soft">
                                    {{ $page }}
                                </a>
                            @endif
                        @elseif (!$separatorShown)
                            @php $separatorShown = true; @endphp
                            <span
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded border border-gray-300 bg-white px-3 text-base font-semibold text-black">
                                .....
                            </span>
                        @endif
                    @endfor
                </div>

                <div class="flex justify-center lg:justify-end">
                    @if ($customers->hasMorePages())
                        <a href="{{ $customers->nextPageUrl() }}"
                            class="inline-flex h-11 items-center gap-3 rounded-lg bg-white px-4 text-base text-black shadow-sm ring-1 ring-gray-100 transition hover:bg-gray-50">
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0-6-6m6 6-6 6" />
                            </svg>
                        </a>
                    @else
                        <span
                            class="inline-flex h-11 cursor-not-allowed items-center gap-3 rounded-lg bg-white px-4 text-base text-gray-400 shadow-sm ring-1 ring-gray-100">
                            Selanjutnya
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m0 0-6-6m6 6-6 6" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </section>

    <div id="detailModal" class="fixed inset-0 z-[70] hidden items-center justify-center overflow-y-auto bg-black/35 px-4 py-6">
        <div class="max-h-[calc(100vh-3rem)] w-full max-w-2xl overflow-y-auto rounded-lg border border-gray-100 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b px-6 py-4">
                <h3 class="text-xl font-bold text-black">Detail Profil Customer</h3>
                <button type="button" onclick="closeModal()"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Tutup modal">
                    &times;
                </button>
            </div>

            <div class="p-5 sm:p-6">
                <div class="mb-6 flex items-center gap-4">
                    <img id="modalFoto" src="" alt="Foto customer"
                        class="h-16 w-16 rounded-full border border-gray-200 object-cover">
                    <div class="min-w-0">
                        <h4 id="modalUsernameTitle" class="truncate text-lg font-bold text-gray-900"></h4>
                        <p id="modalEmailTitle" class="truncate text-sm text-gray-500"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Username</label>
                        <input type="text" id="modalUsername" readonly
                            class="h-11 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Tanggal Lahir</label>
                        <input type="text" id="modalTanggalLahir" readonly
                            class="h-11 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                        <input type="text" id="modalJenisKelamin" readonly
                            class="h-11 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Email</label>
                        <input type="text" id="modalEmail" readonly
                            class="h-11 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Nomor HP</label>
                        <input type="text" id="modalNomorHp" readonly
                            class="h-11 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Status</label>
                        <input type="text" id="modalStatus" readonly
                            class="h-11 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end px-5 pb-5 sm:px-6 sm:pb-6">
                <button type="button" onclick="closeModal()"
                    class="h-10 rounded-lg border border-gray-200 bg-white px-6 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Kembali
                </button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            fetch(`/admin/akun-customer/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalFoto').src = data.foto_url;
                    document.getElementById('modalUsernameTitle').innerText = data.username;
                    document.getElementById('modalEmailTitle').innerText = data.email;
                    document.getElementById('modalUsername').value = data.username;
                    document.getElementById('modalTanggalLahir').value = data.tanggal_lahir;
                    document.getElementById('modalJenisKelamin').value = data.jenis_kelamin;
                    document.getElementById('modalEmail').value = data.email;
                    document.getElementById('modalNomorHp').value = data.nomor_hp ?? '-';
                    document.getElementById('modalStatus').value = data.status;
                })
                .catch(() => {
                    closeModal();
                    alert('Gagal mengambil data dari server.');
                });
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.querySelectorAll('.customer-status-form').forEach((form) => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const action = form.dataset.action;
                const username = form.dataset.username;
                const isDeactivate = action === 'nonaktifkan';

                Swal.fire({
                    icon: 'warning',
                    title: `${isDeactivate ? 'Nonaktifkan' : 'Aktifkan'} customer?`,
                    text: isDeactivate
                        ? `Customer ${username} tidak akan bisa login sampai diaktifkan kembali.`
                        : `Customer ${username} akan bisa login kembali.`,
                    showCancelButton: true,
                    confirmButtonText: `Ya, ${action}`,
                    cancelButtonText: 'Batal',
                    confirmButtonColor: themeColor(isDeactivate ? 'danger' : 'primary'),
                    cancelButtonColor: themeColor('muted'),
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-layouts.admin>
