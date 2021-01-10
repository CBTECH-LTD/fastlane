<x-fl-app-layout>
    <x-slot name="title">{{ $meta->get('entryType.label.plural') }}</x-slot>
    <x-slot name="actions">
        {{-- SLOT: ACTIONS --}}
        @if (isset($actions))
            {{ $actions }}
        @else
            @if ($links->has('create'))
                <x-fl-button href="{{  $links->get('create') }}" left-icon="plus" size="lg">@lang('fastlane::core.add')</x-fl-button>
            @endif
        @endif
        {{-- / SLOT: ACTIONS --}}
    </x-slot>

    {{-- Before Table --}}
    {{ $beforeTable ?? '' }}

    {{-- Table --}}
    <livewire:fl-listing-table :entry-type="$meta->get('entryType.class')" :items-per-page="$meta->get('itemsPerPage')" :order="$meta->get('order_str')"/>

    {{-- After Table --}}
    {{ $afterTable ?? '' }}
</x-fl-app-layout>
