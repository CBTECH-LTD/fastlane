@component('fastlane::app')
    <div class="flex flex-col">
        <div class="fixed top-0 w-full h-20 bg-white border-b border-gray-200 z-40 flex justify-between">
            {{-- App Logo --}}
            <div class="h-20 p-2">
                <img src="{{ asset(config('fastlane.asset_logo_img')) }}" alt="" class="h-full">
            </div>

            {{-- User / Sign out --}}
            <div class="flex items-center p-1 border-t border-gray-300">
                <div class="text-xs mr-16">
                    <strong class="block font-semibold text-gray-800">{{ auth()->user()->name }}</strong>
                    <span class="block font-normal text-gray-600">{{ auth()->user()->email }}</span>
                </div>
                <form action="{{ route('fastlane.cp.logout') }}" method="POST">
                    @csrf
                    <x-fl-button submit variant="minimal" color="black" size="lg" class="text-2xl">
                        <x-fl-icon name="sign-out"></x-fl-icon>
                    </x-fl-button>
                </form>
            </div>
        </div>
        <div class="mt-20 w-full flex">
            {{-- Navigation items --}}
            <div class="flex flex-col h-screen sticky overflow-hidden" style="width: 320px; top: 80px;">
                <div class="flex-grow overflow-y-auto overflow-x-hidden custom-scroll">
                    <x-fl-menu-wrapper class="my-6 px-2" :items="$menuItems()"></x-fl-menu-wrapper>
                </div>
            </div>

            {{-- Main Area --}}
            <div class="w-full flex flex-row overflow-x-hidden px-4">
                @isset($sidebar))
                <div>{{ $sidebar }}</div>
                @endisset
                <div class="flex-grow mb-8" style="border-radius: 2rem">
                    @isset($title)
                        <div x-data="fl.StickyTitleBar()" x-init="init()" class="title-bar-wrapper">
                            <div class="title-bar">
                                <div class="w-4/6">
                                    <h1 class="title-bar__title">
                                        {{ $title }}
                                    </h1>
                                    <div class="title-bar__subtitle">
                                        {{ $subtitle ?? '' }}
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    {{ $actions ?? '' }}
                                </div>
                            </div>
                        </div>
                    @endisset

                    <livewire:fl-flash-messages></livewire:fl-flash-messages>

                    <div class="mt-4 mb-12 px-8 w-full">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcomponent
