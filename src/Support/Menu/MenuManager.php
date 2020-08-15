<?php

namespace CbtechLtd\Fastlane\Support\Menu;

use CbtechLtd\Fastlane\Support\Menu\Contracts;
use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MenuManager implements Contracts\MenuManager
{
    public function build(Contracts\Menu $menu): array
    {
        $user = Auth::user();

        return Collection::make($menu->items())->map(function (MenuItem $item) use ($user) {
            return $item->build($user);
        })->filter()->all();
    }
}
