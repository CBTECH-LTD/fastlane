<div class="block mb-2">
    <a href="{{ $href }}" class="relative py-2 px-4 flex items-center transition-all ease-in-out duration-300 rounded-lg {{ $classes }}">
        <span class="flex text-2xl font-semibold justify-start w-8 opacity-75">
            @isset($icon)
                <x-fl-icon name="{{ $icon }}"></x-fl-icon>
            @endisset
        </span>
        <span class="flex-1 text-sm font-medium">{{ $label }}</span>
    </a>
</div>
