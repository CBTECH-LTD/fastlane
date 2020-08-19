<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use Illuminate\Support\Collection;

interface RenderableOnMenu
{
    public function isVisibleOnMenu(): bool;

    public function renderOnMenu(Collection $menu): void;
}
