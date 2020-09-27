<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use CbtechLtd\Fastlane\Support\Menu\MenuBuilder;
use Illuminate\View\Component;

class AppLayout extends Component
{
    public MenuBuilder $menu;

    public function __construct()
    {
        $this->menu = new MenuBuilder;
    }

    public function menuItems(): array
    {
        return $this->menu->items();
    }

    public function render()
    {
        return view('fastlane::components.app-layout');
    }
}
