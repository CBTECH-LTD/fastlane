<x-fl-boxed-card spaceless>
    {{ $title ?? '' }}

    @if (empty($items))
        <div class="p-8 flex text-center items-center justify-center text-base text-gray-700 font-semibold">
            <div class="w-full p-6">
                <div class="text-6xl text-orange-400 font-normal">
                    <x-fl-icon name="exclamation-triangle"></x-fl-icon>
                </div>
                <strong class="block text-gray-700 mb-2">
                    {{ $emptyTitle ?? __('fastlane::core.empty_lists') }}
                </strong>
                <span class="text-gray-600 mb-8">
                    {{ $emptyMessage ?? '' }}
                </span>
            </div>
        </div>
    @else
        <table class="w-full {{ $autoSize ? 'table-auto' : 'table-fixed' }}">
            <thead>
            <tr class="table__column-group">
                {{ $columns ?? '' }}
            </tr>
            </thead>
            <tbody>
            @foreach ($items as $key => $item)
                <tr class="table__row">
                    {{ ${'row_'.\Illuminate\Support\Str::slug($item->id(), '_')} ?? '' }}
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="table__column-group">
                {{ $columns ?? '' }}
            </tr>
            </tfoot>
        </table>

        {{ $footer ?? '' }}
    @endif
</x-fl-boxed-card>
