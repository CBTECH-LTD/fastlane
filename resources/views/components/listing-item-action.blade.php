@if ($onClick !== '')
    <x-fl-button variant="minimal" :color="$color" title="{{ $title }}" wire:click="{{ $onClick }}">
        <x-fl-icon data-loaded class="text-2xl" name="{{ $icon }}"></x-fl-icon> {{ $slot }}
    </x-fl-button>
@else
    <x-fl-button variant="minimal" :color="$color" title="{{ $title }}" :href="$href">
        <x-fl-icon class="text-2xl" name="{{ $icon }}"></x-fl-icon> {{ $slot }}
    </x-fl-button>
@endif
