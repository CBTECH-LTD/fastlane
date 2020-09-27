<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Support\Menu\Contracts;

class MenuManager implements Contracts\MenuManager
{
    public function build(Contracts\Menu $menu): array
    {
        return $menu->items();
    }
}
