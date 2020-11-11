<div x-data="{ value: {{ $value ? 'true' : 'false' }} }" class="relative my-2">
    <input type="radio" name="{{ $field->getAttribute() }}" x-model="value" class="hidden" value="1">
    <input type="radio" name="{{ $field->getAttribute() }}" x-model="value" class="hidden" value="0">
    <x-fl-button x-show="value === true" variant="solid" title="Click to toggle off" color="green" right-icon="toggle-on" class="rounded-full" @click="value = false">
        {{ $field->getTextTrue() }}
    </x-fl-button>
    <x-fl-button x-show="value === false" variant="outline" title="Click to toggle on" color="black" right-icon="toggle-off" class="rounded-full" @click="value = true">
        {{ $field->getTextFalse() }}
    </x-fl-button>
</div>
