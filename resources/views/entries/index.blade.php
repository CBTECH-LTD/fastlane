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
    <x-fl-table-card :items="$data">
        <x-slot name="columns">
            @foreach ($meta->get('entryType.schema') as $column)
                <th class="table__column" style="width: {{ $column->getListingColWidth() }};">
                    <span class="flex items-center">
                        {{ $column->getLabel() }}

                        @if ($column->isSortable())
                            <x-fl-button href="{{ request()->fullUrlWithQuery(['page' => 1, 'order' => ($meta->get('order.isDesc') ? '' : '-') . $column->getAttribute()]) }}" variant="minimal"
                                         color="{{ $meta->get('order.field') === $column->getAttribute() ? 'black' : 'gray' }}" class="ml-2">
                                @if ($meta->get('order.isDesc'))
                                    <x-fl-icon name="sort-alpha-down-alt"></x-fl-icon>
                                @else
                                    <x-fl-icon name="sort-alpha-down"></x-fl-icon>
                                @endif
                            </x-fl-button>
                        @endif
                    </span>
                </th>
            @endforeach
            <th style="width: 80px"></th>
        </x-slot>

        @foreach ($data as $key => $item)
            <x-slot :name="'row_'.$item->id">
                @foreach ($meta->get('entryType.schema') as $column)
                    <td class="table__cell">
                        <x-fl-row-cell-renderer :model="$item" :field="$column"></x-fl-row-cell-renderer>
                    </td>
                @endforeach
                <td class="table__cell">
                    <div class="w-full h-full flex items-center justify-end">
                        {{-- Edit --}}
                        @if ($meta->entryType->routes->has('edit'))
                            <x-fl-listing-item-action href="{{ $meta->entryType->routes->get('edit')->url($item) }}" icon="pencil-alt" title="Edit"></x-fl-listing-item-action>
                        @endif
                    </div>
                </td>
            </x-slot>
        @endforeach

        @if ($paginator)
            <x-slot name="footer">
                <x-fl-paginator :paginator="$paginator" class="my-4"></x-fl-paginator>
            </x-slot>
        @endif
    </x-fl-table-card>

    {{-- After Table --}}
    {{ $afterTable ?? '' }}
</x-fl-app-layout>
