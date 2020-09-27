<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\View\Component;

class Spinner extends Component
{
    public function render()
    {
        return <<<'blade'
            <div class="spinner"></div>
        blade;
    }
}
