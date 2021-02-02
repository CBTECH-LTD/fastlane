<x-fl-app-layout>
    <x-fl-app-main-area>
        <x-slot name="title">{{ $entryType::label()['plural'] }}</x-slot>
        <x-slot name="actions">
            {{-- SLOT
                Render actions in the top of the page.
            --}}
            @if (isset($actions))
                {{ $actions }}
            @else
                <x-fl-button href="{{ $links['create'] }}" left-icon="plus" size="lg">@lang('fastlane::core.add')</x-fl-button>
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
        <livewire:fl-listing-table :entry-type="$entryType" :items-per-page="$itemsPerPage" :order="$order['str']"/>

        {{-- SLOT
            Optionally render content after the listing table.
        --}}
        {{ $afterTable ?? '' }}
    </x-fl-app-main-area>
</x-fl-app-layout>
