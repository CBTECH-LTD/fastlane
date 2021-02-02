<x-fl-app-layout>
    <x-fl-app-main-area>
        <x-slot name="title">{{ $entry->title() }}</x-slot>
        <x-slot name="subtitle">{{ $entry->type()::label()['singular'] }}</x-slot>
        <x-slot name="actions">
            <x-fl-button href="{{ $entry->links()->get('top') }}" variant="minimal" left-icon="arrow-left" size="lg">@lang('fastlane::core.back_to_list')</x-fl-button>

            @isset($links['create'])
                <x-fl-button href="{{ $entry->links()->get('create') }}" variant="outline" left-icon="plus" size="lg" class="ml-4">@lang('fastlane::core.add_more')</x-fl-button>
            @endisset

            <x-fl-button submit form="mainForm" class="ml-4" color="success" size="lg" left-icon="save">
                @lang('fastlane::core.save')
            </x-fl-button>
        </x-slot>

        <div class="mt-4 mb-12 w-full">
            {{-- SLOT
                Optionally render content before the form.
            --}}
            {{ $beforeForm ?? '' }}

            {{-- SLOT
                The form.
            --}}
            <livewire:fl-entry-form form-id="mainForm" :entry="$entry"></livewire:fl-entry-form>

            {{-- SLOT
                Optionally render content after the form.
            --}}
            {{ $afterForm ?? '' }}
        </div>

        @can('delete', $entry->model())
            <x-fl-boxed-card class="mt-8 border-red-500">
                <x-slot name="title">
                <span class="flex items-center text-danger-600">
                    <x-fl-icon name="exclamation-triangle" class="text-2xl mr-2"></x-fl-icon>
                    @lang('fastlane::core.danger_zone')
                </span>
                </x-slot>

                <div>
                    <livewire:fl-item-action-delete :entry="$entry" redirect="{{ $entry->links()->get('top') }}"></livewire:fl-item-action-delete>
                </div>
            </x-fl-boxed-card>
        @endcan
    </x-fl-app-main-area>
</x-fl-app-layout>
