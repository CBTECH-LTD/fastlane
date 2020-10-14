<{{ $buttonTag }} {{ $attributes->merge($buildAttributes()) }}>
@if (!empty($leftIcon))
    <span class="btn-icon btn-icon-l {{ $loading ? 'opacity-0' : '' }}">
            <x-fl-icon name="{{ $leftIcon }}"></x-fl-icon>
        </span>
@endif

<span>{{ $slot }}</span>

@if(!empty($rightIcon))
    <span class="btn-icon btn-icon-r {{ $loading ? 'opacity-0' : '' }}">
            <x-fl-icon name="{{ $rightIcon }}"></x-fl-icon>
        </span>
@endif

<x-fl-spinner wire:loading.class.remove="hidden"></x-fl-spinner>
</{{ $buttonTag }}>
