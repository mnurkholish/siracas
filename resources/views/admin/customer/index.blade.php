<x-layouts.admin>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-[#4d443f]">Data Akun</h2>

            <form action="{{ route('admin.customer.index') }}" method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari berdasarkan nama atau id"
                    class="pl-4 pr-10 py-2 bg-gray-100 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-[#9d8a7b] w-64">
                <button type="submit" class="absolute right-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="bg-[#e6ddcf] text-[#4d443f]">
                        <th class="py-3 px-4 font-semibold">Username</th>
                        <th class="py-3 px-4 font-semibold">Foto Profil</th>
                        <th class="py-3 px-4 font-semibold">Email</th>
                        <th class="py-3 px-4 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4">{{ $customer->username }}</td>
                            <td class="py-4 px-4 flex justify-center">
                                <img src="{{ $customer->foto_profil ? asset('storage/' . $customer->foto_profil) : asset('images/default-avatar.png') }}"
                                    alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                            </td>
                            <td class="py-4 px-4">{{ $customer->email }}</td>
                            <td class="py-4 px-4">
                                <button onclick="openModal({{ $customer->id }})"
                                    class="text-blue-500 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500">Tidak ada data akun customer
                                ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            {{ $customers->links() }}
        </div>
    </div>

    <div id="detailModal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center 
           bg-white/10 backdrop-brightness-50">

        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 border border-gray-200">

            <!-- HEADER -->
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-[#4d443f]">Detail Profil Customer</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>

            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <img id="modalFoto" src="" alt="Foto"
                        class="w-16 h-16 rounded-full object-cover border border-gray-200">
                    <div>
                        <h4 id="modalUsernameTitle" class="text-lg font-bold text-gray-800"></h4>
                        <p id="modalEmailTitle" class="text-sm text-gray-500"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                        <input type="text" id="modalUsername" readonly
                            class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="text" id="modalTanggalLahir" readonly
                            class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                        <input type="text" id="modalJenisKelamin" readonly
                            class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input type="text" id="modalEmail" readonly
                            class="w-full border rounded-lg px-4 py-2 bg-gray-50 text-gray-600 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end">
                <button onclick="closeModal()"
                    class="px-6 py-2 border rounded-lg text-gray-600 hover:bg-gray-100 font-medium">
                    Kembali
                </button>
            </div>

        </div>
    </div>

    <script>
        function openModal(id) {
            // Tampilkan Modal
            document.getElementById('detailModal').classList.remove('hidden');

            // Fetch data ke server Laravel menggunakan Route Show
            fetch(`/admin/akun-customer/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Isi data ke dalam elemen HTML Modal
                    document.getElementById('modalFoto').src = data.foto_url;
                    document.getElementById('modalUsernameTitle').innerText = data.username;
                    document.getElementById('modalEmailTitle').innerText = data.email;

                    document.getElementById('modalUsername').value = data.username;
                    document.getElementById('modalTanggalLahir').value = data.tanggal_lahir;
                    document.getElementById('modalJenisKelamin').value = data.jenis_kelamin;
                    document.getElementById('modalEmail').value = data.email;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Gagal mengambil data dari server.');
                });
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
</x-layouts.admin>
