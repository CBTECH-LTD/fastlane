@if ($onClick !== '')
    <x-fl-button variant="minimal" :color="$color" title="{{ $title }}" wire:click="{{ $onClick }}">
        @if ($loading)
            <x-fl-spinner></x-fl-spinner>
        @else
            <x-fl-icon class="text-2xl" name="{{ $icon }}"></x-fl-icon> {{ $slot }}
        @endif
    </x-fl-button>
@else
    <x-fl-button variant="minimal" :color="$color" title="{{ $title }}" :href="$href">
        @if ($loading)
            <x-fl-spinner></x-fl-spinner>
        @else
            <x-fl-icon class="text-2xl" name="{{ $icon }}"></x-fl-icon> {{ $slot }}
        @endif
    </x-fl-button>
@endif
