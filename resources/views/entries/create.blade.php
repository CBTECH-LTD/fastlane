<x-fl-app-layout>
    <x-fl-app-main-area>
        <x-slot name="title">@lang('fastlane::core.new') {{ $entry->type()::label()['singular'] }}</x-slot>
        <x-slot name="actions">
            <x-fl-button href="{{ $entry->links()->get('top') }}" variant="minimal" left-icon="arrow-left" size="lg">@lang('fastlane::core.back_to_list')</x-fl-button>

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
            <livewire:fl-entry-form formId="mainForm" :entry="$entry"></livewire:fl-entry-form>

            {{-- SLOT
                Optionally render content after the form.
            --}}
            {{ $afterForm ?? '' }}
        </div>
    </x-fl-app-main-area>
</x-fl-app-layout>
