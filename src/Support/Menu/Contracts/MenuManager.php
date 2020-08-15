<?php

namespace CbtechLtd\Fastlane\Support\Menu\Contracts;

interface MenuManager
{
    public function build(Menu $menu): array;
}
