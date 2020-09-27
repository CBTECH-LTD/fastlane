<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\View\Component;

class TableCard extends Component
{
    public array $items;
    public bool $autoSize;

    public function __construct(array $items, bool $autoSize = false)
    {
        $this->items = $items;
        $this->autoSize = $autoSize;
    }


    public function render()
    {
        return view('fastlane::components.table-card');
    }
}
