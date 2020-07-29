<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Concerns;

trait Makeable
{
    public static function make(...$attributes)
    {
        return new static(...$attributes);
    }
}
