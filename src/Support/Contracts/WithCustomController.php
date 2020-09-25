<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

interface WithCustomController
{
    public static function getController(): string;
}
