<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component as LaravelComponent;

abstract class Component extends LaravelComponent
{
    public static function tag(): string
    {
        $name = (new \ReflectionClass(static::class))->getShortName();

        return 'fl-' . Str::snake($name, '-');
    }
}
