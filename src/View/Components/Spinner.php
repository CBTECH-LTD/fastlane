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
        <div class="w-full h-full flex justify-center items-center absolute bg-white bg-opacity-75 {{ $active ? '' : 'hidden' }}" wire:loading.class.remove="hidden">
            <div {{ $attributes->merge(['class' => 'spinner']) }}></div>
        </div>
        blade;
    }
}
