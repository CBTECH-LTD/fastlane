<div class="p-6">
    @if ($field->getLabel())
        <h2 class="flex items-start justify-start font-light text-gray-900 text-xl mb-6">
            @if ($field->getIcon())
                <span class="pr-4">
                    <x-fl-icon :name="$field->getIcon()"></x-fl-icon>
                </span>
            @endif
            {{ $field->getLabel() }}
        </h2>
    @endif

    @foreach ($field->getFields() as $child)
        @if (is_a($child->formComponent(), \CbtechLtd\Fastlane\View\Components\Form\ReactiveFieldComponent::class, true))
            @livewire($child->formComponent()::tag(), [
                'attribute' => $child->getAttribute(),
                'entry' => $entry,
            ], key($child->getAttribute()))
        @else
            <div wire:key="{{ $child->getAttribute() }}">
                <x-dynamic-component :component="$child->formComponent()::tag()" :field="$child" :entry="$entry"></x-dynamic-component>
            </div>
        @endif
    @endforeach
</div>
