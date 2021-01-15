<div>
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
