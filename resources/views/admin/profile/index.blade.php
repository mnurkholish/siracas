@php
    $user = Auth::user();
    $profilePhoto = $user->foto_profil ? asset('storage/' . $user->foto_profil) : null;
    $birthValue = $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d') : '';
    $birthDisplay = $user->tanggal_lahir ? \Carbon\Carbon::parse($user->tanggal_lahir)->format('d - F - Y') : '-';
    $genderDisplay =
        $user->jenis_kelamin === 'laki-laki' ? 'Laki-laki' : ($user->jenis_kelamin === 'perempuan' ? 'Perempuan' : '-');
@endphp

<x-layouts.admin title="Akun Saya">
    <div x-data="{ activeModal: '{{ $errors->profileUpdate->any() ? 'edit' : ($errors->passwordUpdate->any() ? 'password' : '') }}' }" @keydown.escape.window="activeModal = ''">
        <section>
            <div class="mb-5 rounded-lg bg-secondary px-6 py-6 sm:px-8">
                <h2 class="mb-5 text-sm font-bold text-black">Profil</h2>
                <div class="flex items-center gap-5">
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-400 text-white">
                        @if ($profilePhoto)
                            <img src="{{ $profilePhoto }}" alt="Foto profil {{ $user->username }}"
                                class="h-full w-full object-cover">
                        @else
                            <x-icons.user />
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-base font-bold text-muted-dark">{{ $user->username }}</p>
                        <p class="truncate text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-100 bg-white px-6 py-6 shadow-sm sm:px-8">
                <h2 class="mb-7 text-lg font-bold text-muted-dark">Informasi Pribadi</h2>

                <div class="grid grid-cols-1 gap-x-10 gap-y-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-black">Username</label>
                        <input type="text" value="{{ $user->username }}" readonly
                            class="h-12 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-black">Jenis Kelamin</label>
                        <input type="text" value="{{ $genderDisplay }}" readonly
                            class="h-12 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-black">Tanggal Lahir</label>
                        <div class="relative">
                            <input type="text" value="{{ $birthDisplay }}" readonly
                                class="h-12 w-full rounded-md border border-gray-200 bg-gray-50 px-4 pr-11 text-sm text-gray-700 outline-none">
                            <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-black"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3M5 11h14M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-black">Email</label>
                        <input value="{{ $user->email }}" readonly
                            class="h-12 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-black">Nomor HP</label>
                        <input type="text" value="{{ $user->nomor_hp ?? '-' }}" readonly
                            class="h-12 w-full rounded-md border border-gray-200 bg-gray-50 px-4 text-sm text-gray-700 outline-none">
                    </div>
                </div>

                <div class="mt-10 grid gap-3 sm:flex sm:flex-wrap sm:items-center sm:justify-end">
                    <button type="button" @click="activeModal = 'edit'"
                        class="h-10 rounded-lg bg-primary px-6 text-sm font-semibold text-white transition hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                        Ubah Akun
                    </button>
                    <button type="button" @click="activeModal = 'password'"
                        class="h-10 rounded-lg bg-primary px-6 text-sm font-semibold text-white transition hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                        Ubah Password
                    </button>
                    <button form="logout-form" type="submit"
                        class="h-10 rounded-lg bg-danger px-6 text-sm font-semibold text-white transition hover:bg-danger-dark focus:outline-none focus:ring-2 focus:ring-red-200">
                        Logout
                    </button>
                </div>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </section>

        <div x-show="activeModal === 'edit'" x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/35 px-4 py-8"
            x-transition.opacity>
            <div @click.outside="activeModal = ''" class="max-h-[calc(100vh-3rem)] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-5 shadow-2xl sm:p-8"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-3 scale-95">
                <h2 class="mb-6 text-lg font-bold text-black">Ubah Akun</h2>

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-6 flex flex-wrap items-center gap-5" x-data="{
                        photoPreview: null,
                        removePhoto: false,
                        updatePreview(event) {
                            const file = event.target.files[0];
                            if (!file) return;
                            this.photoPreview = URL.createObjectURL(file);
                            this.removePhoto = false;
                        },
                        clearPhoto() {
                            this.photoPreview = null;
                            this.removePhoto = true;
                            this.$refs.photoInput.value = '';
                        }
                    }">
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-400 text-white">
                            <template x-if="photoPreview && !removePhoto">
                                <img :src="photoPreview" alt="Preview foto profil"
                                    class="h-full w-full object-cover">
                            </template>

                            <template x-if="!photoPreview && !removePhoto">
                                @if ($profilePhoto)
                                    <img src="{{ $profilePhoto }}" alt="Foto profil {{ $user->username }}"
                                        class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center">
                                        <x-icons.user />
                                    </div>
                                @endif
                            </template>

                            <template x-if="removePhoto">
                                <div class="flex h-full w-full items-center justify-center">
                                    <x-icons.user />
                                </div>
                            </template>
                        </div>

                        <div class="gap-3 hidden">
                            <input x-ref="photoInput" id="admin_foto_profil" type="file" name="foto_profil"
                                accept="image/*" class="hidden" @change="updatePreview($event)">
                            <label for="admin_foto_profil"
                                class="inline-flex h-10 cursor-pointer items-center rounded-lg bg-primary px-5 text-sm font-semibold text-white transition hover:bg-primary-dark">
                                Unggah Foto
                            </label>
                            <button type="button" @click="clearPhoto()"
                                class="h-10 rounded-lg border border-gray-200 bg-white px-5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                Hapus
                            </button>
                        </div>

                        <input type="hidden" name="hapus_foto" :value="removePhoto ? 1 : 0">

                        @error('foto_profil', 'profileUpdate')
                            <p class="basis-full text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <p class="text-base font-bold text-muted-dark">{{ $user->username }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-black">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                class="h-12 w-full rounded-md border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-primary focus:ring-2 focus:ring-border-soft">
                            @error('username', 'profileUpdate')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-black">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $birthValue) }}"
                                class="h-12 w-full rounded-md border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-primary focus:ring-2 focus:ring-border-soft">
                            @error('tanggal_lahir', 'profileUpdate')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-black">Jenis Kelamin</label>
                            <select name="jenis_kelamin"
                                class="h-12 w-full rounded-md border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-primary focus:ring-2 focus:ring-border-soft">
                                <option value="">Pilih jenis kelamin</option>
                                <option value="laki-laki" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'laki-laki')>Laki-laki</option>
                                <option value="perempuan" @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'perempuan')>Perempuan</option>
                            </select>
                            @error('jenis_kelamin', 'profileUpdate')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-black">Email</label>
                            <input name="email" value="{{ old('email', $user->email) }}"
                                class="h-12 w-full rounded-md border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-primary focus:ring-2 focus:ring-border-soft">
                            @error('email', 'profileUpdate')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-bold text-black">Nomor HP</label>
                            <input name="nomor_hp" value="{{ old('nomor_hp', $user->nomor_hp) }}"
                                class="h-12 w-full rounded-md border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-primary focus:ring-2 focus:ring-border-soft">

                            @error('nomor_hp', 'profileUpdate')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 grid gap-3 sm:flex sm:flex-wrap sm:justify-end">
                        <button type="submit"
                            class="h-10 rounded-lg bg-primary px-6 text-sm font-semibold text-white transition hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="activeModal = ''"
                            class="h-10 rounded-lg border border-gray-200 bg-white px-6 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="activeModal === 'password'" x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-black/35 px-4 py-8"
            x-transition.opacity>
            <div @click.outside="activeModal = ''" class="max-h-[calc(100vh-3rem)] w-full max-w-lg overflow-y-auto rounded-lg bg-white p-5 shadow-2xl sm:p-8"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-3 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-3 scale-95">
                <h2 class="mb-2 text-lg font-bold text-black">Reset Password</h2>
                <p class="mb-6 text-sm text-gray-500">Masukkan password saat ini sebelum membuat password baru.</p>

                <form action="{{ route('admin.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <div class="space-y-5">
                            <x-auth.input label="Password Saat Ini" name="current_password" type="password"
                                placeholder="Masukkan password saat ini" autocomplete="current-password"
                                error-bag="passwordUpdate" />

                            <x-auth.input label="Password Baru" name="password" type="password"
                                placeholder="Masukkan password baru" autocomplete="new-password"
                                error-bag="passwordUpdate" />

                            <x-auth.input label="Konfirmasi Password Baru" name="password_confirmation"
                                type="password" placeholder="Konfirmasi password baru" autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="mt-8 grid gap-3 sm:flex sm:flex-wrap sm:justify-end">
                        <button type="submit"
                            class="h-10 rounded-lg bg-primary px-6 text-sm font-semibold text-white transition hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                            Simpan Password
                        </button>
                        <button type="button" @click="activeModal = ''"
                            class="h-10 rounded-lg border border-gray-200 bg-white px-6 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
