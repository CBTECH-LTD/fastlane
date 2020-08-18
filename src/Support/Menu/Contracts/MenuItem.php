<?php

namespace CbtechLtd\Fastlane\Support\Menu\Contracts;

interface MenuItem
{
    public function getGroup(): string;

    public function when(\Closure $whenFn): self;

    public function build($user): ?array;
}
