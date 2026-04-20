<x-layouts.public>
    <x-home.navbar />
    <main x-data="{ isEditModalOpen: false }" class="mx-auto max-w-7xl px-6 py-10 lg:px-10">

        <h1 class="mb-6 text-2xl font-bold text-[#4d443f]">Akun Saya</h1>

        <div class="mb-8 flex items-center gap-5 rounded-2xl bg-[#eaddd5] px-8 py-6">
            <div
                class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#9ca3af] text-white">
                @if (Auth::user()->foto_profil)
                    <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" alt="Foto Profil"
                        class="h-full w-full object-cover">
                @else
                    <x-icons.user />
                @endif
            </div>
            <div>
                <h2 class="text-xl font-bold text-[#4d443f]">{{ Auth::user()->username ?? 'Nama Pengguna' }}</h2>
                <p class="text-sm font-medium text-gray-500">{{ Auth::user()->email ?? 'email@example.com' }}</p>
            </div>
        </div>

        <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
            <h3 class="mb-6 text-lg font-bold text-[#4d443f]">Informasi Pribadi</h3>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-[#4d443f]">Username</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        {{ Auth::user()->username }}
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#4d443f]">Jenis Kelamin</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        {{ Auth::user()->jenis_kelamin == 'laki-laki' ? 'Laki-laki' : (Auth::user()->jenis_kelamin == 'perempuan' ? 'Perempuan' : '-') }}
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#4d443f]">Tanggal Lahir</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        {{ Auth::user()->tanggal_lahir ? \Carbon\Carbon::parse(Auth::user()->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-[#4d443f]">Email</label>
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        {{ Auth::user()->email }}
                    </div>
                </div>
            </div>

            <div class="mt-10 flex flex-wrap items-center justify-end gap-3">
                <button type="button" @click="isEditModalOpen = true"
                    class="cursor-pointer rounded-lg bg-[#9e836f] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#8a725f]">
                    Edit Profile
                </button>

                <button form="logout-form" type="submit"
                    class="cursor-pointer rounded-lg bg-[#ef4444] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#dc2626]">
                    Logout
                </button>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>


        <div x-show="isEditModalOpen" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/50 p-4 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <div @click.away="isEditModalOpen = false"
                class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <h2 class="mb-6 text-xl font-bold text-[#4d443f]">Ubah Akun</h2>

                <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="hapus_foto" :value="isDeleted ? 1 : 0">

                    <div class="mb-6 flex items-center gap-5" x-data="{
                        photoPreview: null,
                        removePhoto: false,
                    
                        updatePreview(event) {
                            const file = event.target.files[0];
                            if (file) {
                                this.photoPreview = URL.createObjectURL(file);
                                this.removePhoto = false;
                            }
                        },
                    
                        clearPhoto() {
                            this.photoPreview = null;
                            this.removePhoto = true;
                            $refs.fotoProfilInput.value = '';
                        }
                    }">

                        <!-- PREVIEW FOTO -->
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#9ca3af] text-white">

                            <!-- jika hapus -->
                            <template x-if="removePhoto">
                                <x-icons.user />
                            </template>

                            <!-- jika upload baru -->
                            <template x-if="photoPreview && !removePhoto">
                                <img :src="photoPreview" class="h-full w-full object-cover">
                            </template>

                            <!-- default -->
                            <template x-if="!photoPreview && !removePhoto">
                                @if (Auth::user()->foto_profil)
                                    <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <x-icons.user />
                                @endif
                            </template>

                        </div>

                        <!-- BUTTON -->
                        <div class="flex gap-3">

                            <!-- input file -->
                            <input type="file" name="foto_profil" id="foto_profil" class="hidden" accept="image/*"
                                x-ref="fotoProfilInput" @change="updatePreview($event)">

                            <label for="foto_profil"
                                class="cursor-pointer rounded-lg bg-[#9e836f] px-4 py-2 text-sm font-semibold text-white hover:bg-[#8a725f]">
                                Unggah Foto
                            </label>

                            <!-- tombol hapus -->
                            <button type="button" @click="clearPhoto()"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                Hapus
                            </button>

                        </div>

                        <input type="hidden" name="hapus_foto" :value="removePhoto ? 1 : 0">
                    </div>

                    <div class="mb-6">
                        <h3 class="text-base font-bold text-[#4d443f]">{{ Auth::user()->username }}</h3>
                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="mb-1 block text-sm font-bold text-[#4d443f]">Username</label>
                            <input type="text" name="username" value="{{ old('username', Auth::user()->username) }}"
                                class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-[#a98e79] focus:ring-[#a98e79]"
                                required>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-bold text-[#4d443f]">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', Auth::user()->tanggal_lahir) }}"
                                class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-[#a98e79] focus:ring-[#a98e79]">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-bold text-[#4d443f]">Jenis Kelamin</label>
                            <select name="jenis_kelamin"
                                class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-[#a98e79] focus:ring-[#a98e79]">
                                <option value="laki-laki"
                                    {{ old('jenis_kelamin', Auth::user()->jenis_kelamin) == 'laki-laki' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="perempuan"
                                    {{ old('jenis_kelamin', Auth::user()->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-bold text-[#4d443f]">Email</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm focus:border-[#a98e79] focus:ring-[#a98e79]"
                                required>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="submit"
                            class="cursor-pointer rounded-lg bg-[#9e836f] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#8a725f]">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="isEditModalOpen = false"
                            class="cursor-pointer rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</x-layouts.public>
