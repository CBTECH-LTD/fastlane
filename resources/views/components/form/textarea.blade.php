<x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
    <x-slot name="label">{{ $field->getLabel() }}</x-slot>
    <x-slot name="help">{{ $field->getHelp() }}</x-slot>
    <div class="w-full">
        <textarea {{ $attributes->merge([
        'name' => $field->getAttribute(),
        'placeholder' => $field->getPlaceholder(),
        'required' => $field->isRequired() ? 'required' : null,
        'class' => 'w-full form-input',
        'cols' => '30',
        'row' => '5',
    ]) }}>
        {{ $value }}
    </textarea>
    </div>
</x-fl-field>
