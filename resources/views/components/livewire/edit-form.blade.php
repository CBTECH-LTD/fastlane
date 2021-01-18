<form id="{{ $formId }}" wire:submit.prevent="submit">
    <x-fl-boxed-card spaceless>
        @foreach ($this->fields as $field)
            @if (is_a($field->formComponent(), \CbtechLtd\Fastlane\View\Components\Form\ReactiveFieldComponent::class, true))
                @livewire($field->formComponent()::tag(), [
                'attribute' => $field->getAttribute(),
                'entryType' => $entryType,
                'model' => $model,
                ])
            @else
                <x-dynamic-component :component="$field->formComponent()::tag()" :field="$field" :model="$model" :entry-type="$entryType"></x-dynamic-component>
            @endif

            @if (!$loop->last && $field instanceof \CbtechLtd\Fastlane\Fields\Types\Panel)
                <hr class="border-t border-solid border-gray-300 my-4"/>
            @endif
        @endforeach
    </x-fl-boxed-card>
</form>
