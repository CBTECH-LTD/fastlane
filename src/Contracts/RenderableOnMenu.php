<?php

namespace CbtechLtd\Fastlane\Contracts;

use Illuminate\Support\Collection;

interface RenderableOnMenu
{
    public static function isVisibleOnMenu(): bool;

    public static function renderOnMenu(Collection $menu): void;
}
