<x-fl-app-layout>
    <x-slot name="title">{{ $type::pluralName() }}</x-slot>
    <x-slot name="actions">
        @if (isset($actions))
            {{ $actions }}
        @else
            @if ($type::routes()->has('create'))
                <x-fl-button href="{{ $type::routes()->get('create')->url() }}" left-icon="plus" size="lg">@lang('fastlane::core.add')</x-fl-button>
            @endif
        @endif
    </x-slot>

    {{-- Before Table --}}
    {{ $beforeTable ?? '' }}

    {{-- Table --}}

    {{-- After Table --}}
    {{ $afterTable ?? '' }}
</x-fl-app-layout>
