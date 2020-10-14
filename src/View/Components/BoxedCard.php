<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

class BoxedCard extends Component
{
    public string $icon;
    public bool $spaceless;

    public function __construct(string $icon = '', bool $spaceless = true)
    {
        $this->icon = $icon;
        $this->spaceless = $spaceless;
    }

    public function render()
    {
        return view('fastlane::components.boxed-card');
    }
}
