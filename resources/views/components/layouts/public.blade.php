<x-layouts.app>

    <body class="min-h-screen bg-[#f7f2ed] text-[#6d5b50] antialiased">
        {{ $slot }}
        {{-- <script>
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
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 1000
                });
            @endif
        </script> --}}
    </body>
</x-layouts.app>
