<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\View\Component;

class Icon extends Component
{
    public function __construct()
    {
    }

    public function render()
    {
        return <<<'blade'
            <i $attributes></i>
        blade;
    }
}
