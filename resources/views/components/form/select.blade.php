<div wire:ignore>
    <x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
        <x-slot name="label">{{ $field->getLabel() }}</x-slot>
        <x-slot name="help">{{ $field->getHelp() }}</x-slot>
        <div class="w-full">
            {{-- Render checkboxes or radios instead of select if type is set for checkbox --}}
            @if ($field->shouldRenderAsCheckboxes())
                @php $inputType = $field->isMultiple() ? 'checkbox' : 'radio'; @endphp
                <option value=""></option>
                @foreach ($field->getOptions()->all() as $option)
                    <div class="flex items-center mb-2">
                        <input type="{{ $inputType }}" name="{{ $field->getAttribute() }}" class="form-{{ $inputType }}" value="{{ $value }}">
                    </div>
                @endforeach
            @else
                {{-- Otherwise just render a select --}}
                <select class="form-input w-full"
                        name="{{ $field->getAttribute() }}{{ $field->isMultiple() ? '[]' : '' }}"
                        @if ($field->isMultiple()) multiple="multiple" @endif
                        x-data="fl.Select({ attribute: 'data.{{ $field->getAttribute() }}', taggable: {{ $field->isTaggable() ? 'true' : 'false' }}, multiple: {{ $field->isMultiple() ? 'true' : 'false' }} })"
                        x-init="init()">
                    @foreach ($options as $option)
                        <option value="{{ $option->getValue() }}" @if ($option->isSelected()) selected @endif>
                            {{ $option->getLabel() }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>
    </x-fl-field>

</div>
