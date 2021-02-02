<div @if (! $optionsLoaded) wire:init="loadOptions" @endif>
    <x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
        <x-slot name="label">{{ $field->getLabel() }}</x-slot>
        <x-slot name="help">{{ $field->getHelp() }}</x-slot>

        @if ($optionsLoaded)
            <div class="w-full" wire:ignore>
                {{-- Render checkboxes or radios instead of select if type is set for checkbox --}}
                @if ($field->shouldRenderAsCheckboxes())
                    @php $inputType = $field->isMultiple() ? 'checkbox' : 'radio'; @endphp
                    @foreach ($options as $option)
                        <div class="flex items-center mb-2">
                            <input type="{{ $inputType }}" name="{{ $field->getAttribute() }}" class="form-{{ $inputType }}" value="{{ $option['value'] }}" @if ($option['selected']) checked @endif>
                            <span>{{ $option['label'] }}</span>
                        </div>
                    @endforeach
                @else
                    {{-- Otherwise just render a select --}}
                    <select class="form-input w-full"
                            name="{{ $field->getAttribute() }}{{ $field->isMultiple() ? '[]' : '' }}"
                            @if ($field->isMultiple()) multiple="multiple" @endif
                            x-data="fl.Select({ attribute: 'value', taggable: {{ $field->isTaggable() ? 'true' : 'false' }}, multiple: {{ $field->isMultiple() ? 'true' : 'false' }} })"
                            x-init="init()">
                        <option value=""></option>
                        @foreach ($options as $option)
                            <option value="{{ $option['value'] }}" @if ($option['selected']) selected @endif>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @else
            <div class="w-full">
                <div class="form-input">
                    <span class="text-base text-gray-400 font-bold uppercase tracking-loose">Loading options</span>
                </div>
            </div>
        @endif
    </x-fl-field>

</div>
