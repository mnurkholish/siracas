<x-layouts.public>
    <div class="flex items-center justify-center min-h-[70vh]">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Buat Password Baru</h2>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <x-auth.input label="Email" name="email" type="text" :value="old('email', $request->email)" placeholder="Email"
                        :readonly="true" />
                </div>

                <div>
                    <x-auth.input label="Password Baru" name="password" type="password"
                        placeholder="Masukkan password baru" />
                </div>

                <div>
                    <x-auth.input label="Konfirmasi Password" name="password_confirmation" type="password"
                        placeholder="Konfirmasi password" />
                </div>

                <button type="submit"
                    class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-opacity-90 transition-all">
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</x-layouts.public>
