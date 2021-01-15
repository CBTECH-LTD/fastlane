<div class="flex justify-between" x-data="fl.ItemActionDelete()" x-init="init()" x-cloak>
    <div class="flex flex-grow flex-col text-sm">
        <strong class="font-bold text-gray-900">@lang('fastlane::core.delete_entry_title')</strong>
        <span class="font-normal text-gray-800">@lang('fastlane::core.delete_entry_description')</span>
    </div>
    <div class="rounded p-1" :class="classes">
        <x-fl-button x-show="!waitingConfirmation" x-on:click="showConfirmation" color="danger" variant="outline" left-icon="trash">
            @lang('fastlane::core.delete_entry_button')
        </x-fl-button>

        <div x-show="waitingConfirmation" class="flex flex-row items-center">
            <x-fl-button color="black" variant="minimal" left-icon="undo" class="mr-2" x-on:click="cancel">
                @lang('fastlane::core.cancel')
            </x-fl-button>
            <x-fl-button color="danger" left-icon="trash" x-on:click="confirm">
                @lang('fastlane::core.confirm')

                <template x-if="attemptingDelete">
                    <x-fl-spinner :active="true"></x-fl-spinner>
                </template>
            </x-fl-button>
        </div>
    </div>
</div>
