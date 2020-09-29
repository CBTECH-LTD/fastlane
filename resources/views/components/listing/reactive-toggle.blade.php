<div class="w-full flex justify-center items-center my-2">
    @if ($this->value === true)
        <x-fl-listing-item-action on-click="toggleOff" icon="toggle-on" color="green" wire:loading.attr="loading"></x-fl-listing-item-action>
    @else
        <x-fl-listing-item-action on-click="toggleOn" icon="toggle-off" color="black" wire:loading.attr="loading"></x-fl-listing-item-action>
    @endif
</div>
