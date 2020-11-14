<x-fl-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="subtitle">{{ $meta->get('entryType.label.singular') }}</x-slot>
    <x-slot name="actions">
        @if ($links->has('top'))
            <x-fl-button href="{{ $links->get('top') }}" variant="minimal" left-icon="arrow-left" size="lg">@lang('fastlane::core.back_to_list')</x-fl-button>
        @endif
        @if ($links->has('create'))
            <x-fl-button href="{{ $links->get('create') }}" variant="outline" left-icon="plus" size="lg" class="ml-4">@lang('fastlane::core.add_more')</x-fl-button>
        @endif
        <x-fl-button submit form="main" class="ml-4" color="success" size="lg" left-icon="save">
            @lang('fastlane::core.save')
        </x-fl-button>
    </x-slot>

    <livewire:fl-form :action="$links->get('self')" method="update" form-id="main" :fields="$fields" :model="$model" :entry-type="$entryType"></livewire:fl-form>

    @can('delete', $model)
        <x-fl-boxed-card class="mt-8 border-red-500">
            <x-slot name="title">
                <span class="flex items-center text-danger-600">
                    <x-fl-icon name="exclamation-triangle" class="text-2xl mr-2"></x-fl-icon>
                    @lang('fastlane::core.danger_zone')
                </span>
            </x-slot>

            <div>
                <livewire:fl-item-action-delete :entry-type="$entryType" :entry-id="$model->getRouteKey()" url="{{ $links->get('self') }}" redirect="{{ $links->get('top') }}"></livewire:fl-item-action-delete>
            </div>
        </x-fl-boxed-card>
    @endcan
</x-fl-app-layout>
