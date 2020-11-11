<x-fl-app-layout>
    <x-slot name="title">@lang('fastlane::core.new') {{ $meta->get('entryType.label.singular') }}</x-slot>
    <x-slot name="actions">
        @if ($links->has('top'))
            <x-fl-button href="{{ $links->get('top') }}" variant="outline" left-icon="arrow-left" size="lg">@lang('fastlane::core.back_to_list')</x-fl-button>
        @endif
        <x-fl-button submit form="main" class="ml-4" color="success" size="lg" left-icon="save">
            @lang('fastlane::core.save')
        </x-fl-button>
    </x-slot>

    <livewire:fl-form :action="$links->get('self')" :entry-type="$entryType" :fields="$fields" :model="$model" form-id="main" method="create"></livewire:fl-form>
</x-fl-app-layout>
