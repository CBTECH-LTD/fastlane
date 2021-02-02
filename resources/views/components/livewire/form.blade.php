<div>
    <form id="{{ $formId }}" x-data="fl.Form()" x-on:submit.prevent="submit">
        <x-fl-boxed-card spaceless>
            @foreach ($this->fields as $field)
                <div wire:key="{{ $field->getAttribute() }}">
                    @if (is_a($field->formComponent(), \CbtechLtd\Fastlane\View\Components\Form\ReactiveFieldComponent::class, true))
                        @livewire($field->formComponent()::tag(), [
                        'attribute' => $field->getAttribute(),
                        'entry' => $entry,
                        ], key($field->getAttribute()))
                    @else
                        <div wire:key="{{ $field->getAttribute() }}">
                            <x-dynamic-component :component="$field->formComponent()::tag()" :field="$field" :entry="$entry"></x-dynamic-component>
                        </div>
                    @endif

                    @if (!$loop->last && $field instanceof \CbtechLtd\Fastlane\Fields\Types\Panel)
                        <hr class="border-t border-solid border-gray-300 my-4"/>
                    @endif
                </div>
            @endforeach
        </x-fl-boxed-card>
    </form>
</div>
