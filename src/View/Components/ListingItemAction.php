<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

class ListingItemAction extends Component
{
    public string $href;
    public string $icon;
    public string $color;
    public string $title;
    public string $onClick;
    public bool $loading;

    public function __construct(string $href = '', string $icon = '', string $color = 'black', string $title = '', string $onClick = '', bool $loading = false)
    {
        $this->href = $href;
        $this->title = $title;
        $this->icon = $icon;
        $this->color = $color;
        $this->onClick = $onClick;
        $this->loading = $loading;  
    }

    public function render()
    {
        return view('fastlane::components.listing-item-action');
    }
}
