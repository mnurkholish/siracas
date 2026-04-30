<x-layouts.app>

    <body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

        {{-- CONTAINER UTAMA --}}
        <div class="w-full max-w-4xl min-h-[725px] bg-white rounded-xl shadow-xl overflow-hidden flex">

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

        <script>
            @if (session('success'))
                Swal.fire({
                    icon: "success",
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 1500,
                    width: 300,
                    padding: "1em",
                });
            @elseif (session('error_alert'))
                Swal.fire({
                    icon: "error",
                    title: "{{ session('error_alert') }}",
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif
        </script>
    </body>
</x-layouts.app>
