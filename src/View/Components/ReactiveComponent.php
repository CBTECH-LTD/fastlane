<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components;

use Illuminate\Support\Str;

class ReactiveComponent extends \Livewire\Component
{
    public static function tag(): string
    {
        $name = (new \ReflectionClass(static::class))->getShortName();

        return 'fl-' . Str::snake($name, '-');
    }
}
