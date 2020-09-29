<div class="relative w-full flex justify-center items-center my-2">
    @if ($this->value === true)
        <x-fl-listing-item-action on-click="toggleOff" icon="toggle-on" color="green" wire:loading.class="hidden"></x-fl-listing-item-action>
    @else
        <x-fl-listing-item-action on-click="toggleOn" icon="toggle-off" color="black" wire:loading.class="hidden"></x-fl-listing-item-action>
    @endif
    <x-fl-spinner></x-fl-spinner>
</div>
