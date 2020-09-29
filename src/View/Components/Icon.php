<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

class Icon extends Component
{
    public string $name;

    public function __construct(?string $name = null)
    {
        $this->name = $name ?? '';
    }

    public function shouldRender()
    {
        return isset($this->name);
    }

    public function render()
    {
        return <<<'blade'
            <i {{ $attributes->merge(['class' =>'la la-'.$name]) }}></i>
        blade;
    }
}
