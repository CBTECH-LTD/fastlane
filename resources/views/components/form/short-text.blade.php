<x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
    <x-slot name="label">{{ $field->getLabel() }}</x-slot>
    <x-slot name="help">{{ $field->getHelp() }}</x-slot>
    <div class="w-full">
        <input {{ $attributes->merge([
        'type' => $field->getInputType(),
        'name' => $field->getAttribute(),
        'placeholder' => $field->getPlaceholder(),
        'required' => $field->isRequired() ? 'required' : null,
        'class' => 'w-full form-input',
        'value' => $value,
    ]) }}>
    </div>
</x-fl-field>
