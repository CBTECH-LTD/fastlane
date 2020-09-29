<{{ $buttonTag }} {{ $attributes->merge($buildAttributes()) }}>
@if (!empty($leftIcon))
    <span class="btn-icon btn-icon-l {{ $loading ? 'opacity-0' : '' }}">
            <x-fl-icon name="{{ $leftIcon }}"></x-fl-icon>
        </span>
@endif

<span class="{{ $loading ? 'opacity-0' : '' }}">
    {{ $slot }}
</span>

@if(!empty($rightIcon))
    <span class="btn-icon btn-icon-r {{ $loading ? 'opacity-0' : '' }}">
            <x-fl-icon name="{{ $rightIcon }}"></x-fl-icon>
        </span>
@endif

@if ($loading)
    <span class="absolute top-0 left-0 w-full h-full flex items-center justify-center">
            <x-fl-spinner color="info"></x-fl-spinner>
        </span>
@endif
</{{ $buttonTag }}>
