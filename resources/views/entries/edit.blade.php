<x-fl-app-layout>
    <x-slot name="title">{{ $item->getEntryType()->entryTitle() }}</x-slot>
    <x-slot name="subtitle">{{ $type::name() }}</x-slot>
    <x-slot name="actions">
        @if ($type::routes()->has('index'))
            <x-fl-button href="{{ $type::routes()->get('index')->url() }}" variant="outline" left-icon="arrow-left" size="lg">@lang('fastlane::core.back_to_list')</x-fl-button>
        @endif
        <x-fl-button submit form="main" class="ml-4" color="success" size="lg" left-icon="save">
            @lang('fastlane::core.save')
        </x-fl-button>
    </x-slot>

    <form action="{{ $type::routes()->get('update')->url($item) }}" method="POST" id="main">
        @csrf @method('PATCH')

        
    </form>
</x-fl-app-layout>
