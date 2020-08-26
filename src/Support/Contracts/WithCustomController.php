<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use Illuminate\Routing\Router;

interface WithCustomController
{
    public function getController(): string;
}
