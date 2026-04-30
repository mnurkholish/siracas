<x-layouts.auth title="Register">

    <x-auth.card class="animate-fadeDown [animation-delay:200ms]">
        <h2 class="text-2xl font-bold mb-6">Daftar</h2>

        <form method="POST" action="/register">
            @csrf

            <x-auth.input label="Username" name="username" placeholder="Masukkan username" mb="3" />

            <x-auth.input label="Email" name="email" placeholder="Masukkan email" mb="3" />

            <x-auth.input label="Nomor HP" name="nomor_hp" placeholder="Masukkan nomor HP" mb="3" />

            {{-- Select --}}
            <div class="mb-3">
                <label class="block text-sm mb-1 text-gray-700 font-medium">Jenis Kelamin</label>
                <select name="jenis_kelamin"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#9E826D] focus:shadow-md outline-none transition">
                    <option value="" {{ old('jenis_kelamin') ? '' : 'selected' }}>
                        Pilih jenis kelamin
                    </option>

                    <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>
                        Laki-laki
                    </option>

                    <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>
                        Perempuan
                    </option>
                </select>

                @error('jenis_kelamin')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <x-auth.input label="Tanggal Lahir" name="tanggal_lahir" type="date" mb="3" />

            <x-auth.input label="Password" name="password" type="password" placeholder="Masukkan Password"
                mb="3" />

            <x-auth.button type="submit">
                Buat akun
            </x-auth.button>
        </form>

        <p class="text-sm mt-2 text-center">
            Sudah punya akun? <a href="/login" class="text-primary underline">Login</a>
        </p>
    </x-auth.card>

</x-layouts.auth>
