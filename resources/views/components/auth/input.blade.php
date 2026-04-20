@props(['label', 'type' => 'text', 'name', 'placeholder' => '', 'forget' => ''])

<div class="mb-4">
    <label class="block text-sm mb-1 text-gray-700">
        {{ $label }}
    </label>

    <!-- INPUT -->
    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
        value="{{ old($name) }}"
        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#9E826D] focus:shadow-md outline-none transition">

    <!-- CHECKBOX KHUSUS PASSWORD -->
    @if ($type === 'password')
        <div class="flex items-center mt-2 justify-between">
            <div>
                <input type="checkbox" id="show_{{ $name }}"
                    onclick="
                        const p = document.getElementById('{{ $name }}');
                        p.type = this.checked ? 'text' : 'password';
                    "
                    class="mr-2 accent-[#9E826D]">
                <label for="show_{{ $name }}" class="text-sm text-gray-600">
                    Tampilkan Password
                </label>
            </div>
            @if ($forget)
                <a href="{{ route('password.request') }}" class="text-primary hover:underline text-xs">Lupa Password?
                </a>
            @endif
        </div>
    @endif

    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
