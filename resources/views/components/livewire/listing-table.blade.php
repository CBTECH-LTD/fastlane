<div class="relative" @if (!$dataLoaded) wire:init="loadData" @endif>
    @if ($dataLoaded)
        <x-fl-table-card :items="$items">
            <x-slot name="columns">
                @foreach ($meta->get('entryType.schema') as $column)
                    <th class="table__column" style="width: {{ $column->getListingColWidth() }};">
                    <span class="flex items-center">
                        {{ $column->getLabel() }}

                        @if ($column->isSortable())
                            <x-fl-button wire:click="changeOrder('{{ $meta->get('order.dir') === 'desc' && $meta->get('order.field') === $column->getAttribute() ? '' : '-' }}{{ $column->getAttribute() }}')"
                                         variant="minimal"
                                         color="{{ $meta->get('order.field') === $column->getAttribute() ? 'black' : 'gray' }}"
                                         class="ml-2">
                                @if ($meta->get('order.dir') === 'desc' && $meta->get('order.field') === $column->getAttribute())
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

            @foreach ($items as $key => $item)
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
    @else
        <x-fl-boxed-card spaceless>
            <div class="p-8 flex text-center items-center justify-center text-base text-gray-700 font-semibold">
                <div class="w-full p-6">
                    <div class="text-6xl text-brand-500 font-normal">
                        <x-fl-icon name="dolly"></x-fl-icon>
                    </div>
                    <strong class="block text-gray-700 mb-2">
                        {{ __('fastlane::core.loading') }}
                    </strong>
                </div>
            </div>
        </x-fl-boxed-card>
    @endif

    <div wire:loading.class.remove="hidden" class="hidden absolute top-0 left-0 right-0 bottom-0 bg-gray-100 opacity-75 z-50 flex items-center justify-center">
        <x-fl-spinner></x-fl-spinner>
    </div>
</div>
