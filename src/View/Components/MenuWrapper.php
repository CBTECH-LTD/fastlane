<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Support\Collection;

class MenuWrapper extends Component
{
    public array $items;

    public function __construct(array $items)
    {
        $this->items = Collection::make($items)->filter(
            fn(Component $item) => $item->shouldRender()
        )->all();
    }

    public function render()
    {
        return view('fastlane::components.menu-wrapper', [
            'items' => $this->items,
        ]);
    }
}
