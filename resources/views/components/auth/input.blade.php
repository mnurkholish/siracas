@props([
    'label',
    'type' => 'text',
    'name',
    'placeholder' => '',
    'forget' => false,
    'value' => null,
    'readonly' => false,
    'autocomplete' => null,
    'errorBag' => 'default',
    'mb' => 4,
])

<div class="mb-{{ $mb }}">
    <!-- LABEL -->
    <label for="{{ $name }}" class="block text-sm mb-1 text-gray-700">
        {{ $label }}
    </label>

    <!-- INPUT WRAPPER -->
    <div class="relative w-full">
        <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
            placeholder="{{ $placeholder }}" value="{{ $value ?: old($name) }}" {{ $readonly ? 'readonly' : '' }}
            autocomplete="{{ $autocomplete }}"
            class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#9E826D] focus:shadow-md outline-none transition">

        <!-- ICON PASSWORD -->
        @if ($type === 'password')
            <button type="button"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-10 flex items-center text-gray-500 hover:text-gray-700"
                onclick="
                    const input = document.getElementById('{{ $name }}');
                    const eye = document.getElementById('eye_{{ $name }}');
                    const slash = document.getElementById('slash_{{ $name }}');

                    if (input.type === 'password') {
                        input.type = 'text';
                        eye.classList.add('hidden');
                        slash.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        eye.classList.remove('hidden');
                        slash.classList.add('hidden');
                    }
                ">

                <!-- Eye -->
                <svg id="eye_{{ $name }}" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                           c4.477 0 8.268 2.943 9.542 7
                           -1.274 4.057-5.065 7-9.542 7
                           -4.477 0-8.268-2.943-9.542-7z" />
                </svg>

                <!-- Eye Slash -->
                <svg id="slash_{{ $name }}" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587A2 2 0 0012 14
                           a2 2 0 001.414-.586M9.88 4.24A10.45 10.45 0 0112 4
                           c5 0 9.27 3.11 11 7.5a11.72 11.72 0 01-4.35 5.33
                           M6.53 6.53A11.72 11.72 0 001 11.5
                           C2.73 15.89 7 19 12 19a10.7 10.7 0 004.47-.97" />
                </svg>

            </button>
        @endif
    </div>

    <!-- ERROR -->
    @error($name, $errorBag)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror

    <!-- FORGOT PASSWORD -->
    @if ($type === 'password' && $forget)
        <div class="mt-2 text-right">
            <a href="{{ route('password.request') }}" class="text-primary hover:underline text-xs">
                Lupa Password?
            </a>
        </div>
    @endif
</div>
