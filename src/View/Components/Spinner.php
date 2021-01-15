<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

class Spinner extends Component
{
    public bool $active;

    public function __construct(bool $active = false)
    {
        $this->active = $active;
    }

    public function render()
    {
        return <<<'blade'
        <div class="w-full h-full flex justify-center items-center absolute top-0 left-0 right-0 bottom-0 rounded bg-white bg-opacity-75 {{ $active ? '' : 'hidden' }}" wire:loading.class.remove="hidden">
            <div {{ $attributes->merge(['class' => 'spinner']) }}></div>
        </div>
        blade;
    }
}
