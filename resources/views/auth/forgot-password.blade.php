<x-layouts.public>
    <div class="flex items-center justify-center min-h-[70vh]">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h2 class="text-2xl font-bold text-slate-800 mb-2">Lupa Password?</h2>
            <p class="text-slate-500 text-sm mb-6">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk
                mengatur ulang kata sandi Anda.</p>

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <x-auth.input label="Alamat Email" name="email" placeholder="Masukkan email" />

                    <button type="submit"
                        class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-opacity-90 transition-all">
                        Kirim Link Reset
                    </button>
            </form>
        </div>
    </div>
</x-layouts.public>
