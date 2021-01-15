<x-fl-app-layout>
    <x-fl-app-main-area>
        <x-slot name="title">{{ $meta->get('entryType.label.plural') }}</x-slot>
        <x-slot name="actions">
            {{-- SLOT
                Render actions in the top of the page.
            --}}
            @if (isset($actions))
                {{ $actions }}
            @else
                @if ($links->has('create'))
                    <x-fl-button href="{{  $links->get('create') }}" left-icon="plus" size="lg">@lang('fastlane::core.add')</x-fl-button>
                @endif
            @endif
            {{-- / SLOT: ACTIONS --}}
        </x-slot>

        {{-- SLOT
            Optionally render content before the listing table.
        --}}
        {{ $beforeTable ?? '' }}

        {{--
            The listing table.
        --}}
        <livewire:fl-listing-table :entry-type="$meta->get('entryType.class')" :items-per-page="$meta->get('itemsPerPage')" :order="$meta->get('order_str')"/>

        {{-- SLOT
            Optionally render content after the listing table.
        --}}
        {{ $afterTable ?? '' }}
    </x-fl-app-main-area>
</x-fl-app-layout>
