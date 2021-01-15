<div class="flex-grow mb-8" style="border-radius: 2rem">
    <div x-data="fl.StickyTitleBar()" x-init="init()" class="title-bar-wrapper">
        <div class="title-bar">
            <div class="w-4/6">
                <h1 class="title-bar__title">
                    {{ $title }}
                </h1>
                <div class="title-bar__subtitle">
                    {{ $subtitle ?? '' }}
                </div>
            </div>
            <div class="flex items-center">
                @foreach ($actions as $action)
                @endforeach

                <x-fl-button submit form="{{ $formId }}" class="ml-4" color="success" size="lg" left-icon="save">
                    @lang('fastlane::core.save')
                </x-fl-button>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-12 px-8 w-full">
        <form id="{{ $formId }}" wire:submit.prevent="submit">
            <x-fl-boxed-card>
                @foreach ($fields as $field)
                    <x-dynamic-component :component="$field->formComponent()::tag()" :field="$field" :model="$model" :entry-type="$entryType"></x-dynamic-component>
                    @if (!$loop->last && $field instanceof \CbtechLtd\Fastlane\Fields\Types\Panel)
                        <hr class="border-t border-dashed border-gray-500 my-8"/>
                    @endif
                @endforeach
            </x-fl-boxed-card>
        </form>
    </div>
</div>
