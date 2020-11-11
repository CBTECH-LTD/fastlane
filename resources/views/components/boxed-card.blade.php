<div {{ $attributes->merge(['class' => 'rounded-lg bg-white border border-gray-300 shadow-lg']) }}>
    @isset($title)
        <div class="p-4 border-b border-gray-300 text-gray-700 font-semibold text-lg tracking-wider">
            <div class="flex items-center">
                @isset($icon)
                    <x-fl-icon name="{{ $icon }}" class="mr-4 text-xl"></x-fl-icon>
                @endisset

                <div class="flex-grow">{{ $title }}</div>
                <div class="flex-shrink">{{ $actions ?? '' }}</div>
            </div>
        </div>
    @endisset

    <div class="{{ !$spaceless ? 'p-6' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="p-4 border-t border-gray-300">
            {{ $footer }}
        </div>
    @endif
</div>
