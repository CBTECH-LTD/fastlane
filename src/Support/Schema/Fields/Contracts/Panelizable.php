<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;

interface Panelizable
{
    public function inPanel(FieldPanel $panel): self;
}
