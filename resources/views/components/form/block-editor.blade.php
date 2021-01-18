<x-fl-field :errors="$errors->get($field->getAttribute())" :required="$field->isRequired()">
    <x-slot name="label">{{ $field->getLabel() }}</x-slot>
    <x-slot name="help">{{ $field->getHelp() }}</x-slot>
    <div class="w-full">
        {{-- Added blocks --}}
        <div class="w-full p-4">
            @foreach ($blocks as $block)
            @endforeach
        </div>

        {{-- Blocks selector --}}
        <div x-cloak class="w-full flex flex-wrap justify-center my-4" x-data="fl.BlockEditor()">
            <div class="flex justify-center w-48 border-t border-gray-800 border-dashed">
                <x-fl-button x-show="!shouldShowAvailableBlocks" color="black" left-icon="plus" size="sm" class="-mt-4" x-on:click="showAvailableBlocks(0)">
                    {{ __('fastlane::core.add') }}
                </x-fl-button>
                <x-fl-button x-show="shouldShowAvailableBlocks" color="black" left-icon="close" size="sm" class="-mt-4" x-on:click="hideAvailableBlocks()">
                    {{ __('fastlane::core.cancel') }}
                </x-fl-button>
            </div>
            <div x-show="shouldShowAvailableBlocks" class="fixed top-0 left-0 w-full h-screen bg-black bg-opacity-75 flex items-center justify-center overflow-hidden z-40 p-8">
                <div class="relative w-full bg-white border border-gray-200 rounded shadow p-4 overflow-y-auto" style="max-width: 440px" x-on:click.away="hideAvailableBlocks()">
                    <h3 class="font-semibold text-sm text-gray-800 text-center mb-4 pb-4 border-b border-gray-300">{{ __('fastlane::core.select_block') }}</h3>
                    <div class="w-full grid grid-cols-3 gap-4">
                        @foreach ($availableBlocks as $block)
                            <div x-on:click="selectNewBlock('{{ $block::key() }}')" class="rounded h-32 p-2 flex flex-col items-center overflow-hidden cursor-pointer text-gray-700 hover:text-white hover:bg-brand hover:shadow-lg">
                                <div class="w-16 h-16">
                                    <img class="w-full" src="https://via.placeholder.com/64" alt="{{ $block::key() }}">
                                </div>
                                <div class="flex-grow flex items-center text-center text-sm font-normal mt-2">
                                    {{ $block::name() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-fl-field>
