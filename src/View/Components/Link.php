<?php

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Link extends Component
{
    private string $color;
    /**
     * @var bool
     */
    private bool $onBlank;

    /**
     * Create a new component instance.
     *
     * @param string $color
     */
    public function __construct(string $color = 'brand', bool $onBlank = false)
    {
        $this->color = $color;
        $this->onBlank = $onBlank;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return <<<'blade'
            <a {{ $attributes->merge($buildAttributes()) }}>{{ $slot }}</a>
        blade;
    }

    public function buildAttributes(): array
    {
        $classes = Collection::make([
            'class' => "font-medium transition ease-in-out duration-500 focus:outline-none focus:underline hover:underline {$this->getColorClasses()}",
        ]);

        if ($this->onBlank) {
            $classes->put('target', '_blank');
        }

        return $classes->toArray();
    }

    protected function getColorClasses(): string
    {
        if ($this->color === 'white') {
            return 'text-white hover:text-brand-300';
        }

        return "text-{$this->color}-600 hover:text-{$this->color}-700";
    }
}
