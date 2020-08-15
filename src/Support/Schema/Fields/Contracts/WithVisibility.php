<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

interface WithVisibility
{
    public function isShownOnIndex(): bool;

    public function isShownOnCreate(): bool;

    public function isShownOnUpdate(): bool;
}
