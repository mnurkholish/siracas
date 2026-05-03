@props([
    'title' => 'SIRACAS',
])

<x-layouts.app :title="$title">

    <body class="min-h-screen bg-secondary-soft text-text-body antialiased">
        {{ $slot }}
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
