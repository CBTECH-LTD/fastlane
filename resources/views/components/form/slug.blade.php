<x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
    <x-slot name="label">{{ $field->getLabel() }}</x-slot>
    <x-slot name="help">{{ $field->getHelp() }}</x-slot>
    <div class="w-full">
        <input
            class="w-full form-input"
            wire:model.lazy="value"
            type="{{ $field->getInputType() }}"
            name="{{ $field->getAttribute() }}"
            placeholder="{{ $field->getPlaceholder() }}"
            @if ($field->isRequired()) required @endif
    ]) }}>
    </div>
</x-fl-field>
