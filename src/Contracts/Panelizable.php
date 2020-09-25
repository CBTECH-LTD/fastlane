<?php

namespace CbtechLtd\Fastlane\Contracts;

use CbtechLtd\Fastlane\Fields\Types\Panel;

interface Panelizable
{
    public function inPanel(Panel $panel): self;
}
