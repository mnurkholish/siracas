<x-layouts.app>

    <body class="min-h-screen bg-gray-100 flex items-center justify-center">

        {{-- CONTAINER UTAMA --}}
        <div class="w-full max-w-4xl h-175 bg-white rounded-xl shadow-xl overflow-hidden flex">

            <x-auth.back />

            {{-- LEFT (HERO) --}}
            <div class="hidden md:flex w-1/2 relative">
                <x-auth.hero />
            </div>

            {{-- RIGHT (FORM) --}}
            <div class="w-full md:w-1/2 flex items-center justify-center p-8 bg-gray-50">
                {{ $slot }}
            </div>
        </div>
    </body>
</x-layouts.app>
