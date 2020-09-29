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
    <x-fl-table-card :items="$items">
        <x-slot name="columns">
            @foreach ($columns as $column)
                <th class="table__column" style="@if ($column->getListingColWidth()) width: {{ $column->getListingColWidth() }}; @endif">
                    <span class="flex items-center">
                        {{ $column->getLabel() }}
                        @if ($column->isSortable())
                            <x-fl-button @click="orderBy(field)" variant="minimal" color="{{ $orderBy === $column->getAttribute() ? 'black' : 'gray' }}" class="ml-2">
                                @if ($orderDirection === 'asc')
                                    <x-fl-icon name="sort-alpha-down"></x-fl-icon>
                                @endif
                                @if ($orderDirection === 'desc')
                                    <x-fl-icon name="sort-alpha-down-alt"></x-fl-icon>
                                @endif
                            </x-fl-button>
                        @endif
                    </span>
                </th>
            @endforeach
            <th style="width: 80px"></th>
        </x-slot>

        @foreach ($items as $key => $item)
            <x-slot :name="'row_'.$key">
                @foreach ($columns as $column)
                    <td class="table_ex_cell">
                        <x-fl-row-cell-renderer :model="$item" :field="$column"></x-fl-row-cell-renderer>
                    </td>
                @endforeach
                <td class="table__cell">
                    <div class="w-full h-full flex items-center justify-end">
                        {{-- Edit --}}
                        @if ($type::routes()->get('edit'))
                            <x-fl-listing-item-action href="{{ $type::routes()->get('edit')->url($item) }}" icon="pencil-alt" title="Edit"></x-fl-listing-item-action>
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
