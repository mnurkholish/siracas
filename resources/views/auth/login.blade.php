<x-layouts.auth title="Login">

    <x-auth.card class="animate-fadeUp [animation-delay:200ms]">
        <h2 class="text-2xl font-bold mb-6">Login</h2>

        <form method="POST" action="/login">
            @csrf

            <x-auth.input label="Email" name="email" placeholder="Masukkan email" />

            <x-auth.input :forget="true" label="Password" name="password" type="password"
                placeholder="Masukkan password" />

            <x-auth.button type="submit">
                Masuk
            </x-auth.button>
        </form>

        <p class="text-sm mt-2 text-center">
            Belum punya akun? <a href="/register" class="text-primary underline">Daftar</a>
        </p>
    </x-auth.card>

</x-layouts.auth>
