<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Contracts;

interface ContentBlock
{
    public static function key(): string;

    public static function name(): string;

    public static function fields(): array;
}
