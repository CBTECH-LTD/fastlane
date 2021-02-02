<x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
    <x-slot name="label">{{ $field->getLabel() }}</x-slot>
    <x-slot name="help">{{ $field->getHelp() }}</x-slot>
    <div class="w-full">
        <textarea
            class="w-full form-input"
            cols="30" rows="5"
            wire:model.lazy="value"
            name="{{ $field->getAttribute() }}"
            placeholder="{{ $field->getPlaceholder() }}"
            @if ($field->isRequired()) required @endif></textarea>
    </div>
</x-fl-field>
