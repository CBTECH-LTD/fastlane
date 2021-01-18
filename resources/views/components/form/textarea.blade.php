<x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
    <x-slot name="label">{{ $field->getLabel() }}</x-slot>
    <x-slot name="help">{{ $field->getHelp() }}</x-slot>
    <div class="w-full">
        <textarea {{ $attributes->merge([
            'name' => $field->getAttribute(),
            'placeholder' => $field->getPlaceholder(),
            'required' => $field->isRequired() ? 'required' : null,
            'value' => $value,
            'wire:model.lazy' => 'data.' . $field->getAttribute(),
            'class' => 'w-full form-input',
            'cols' => '30',
            'rows' => '5',
        ]) }}></textarea>
    </div>
</x-fl-field>
