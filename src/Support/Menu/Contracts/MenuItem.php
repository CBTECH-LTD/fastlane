<?php

namespace CbtechLtd\Fastlane\Support\Menu\Contracts;

use CbtechLtd\Fastlane\View\Components\Component;

abstract class MenuItem extends Component
{
    abstract public function getGroup(): string;

    abstract public function when(\Closure $whenFn): self;
}
