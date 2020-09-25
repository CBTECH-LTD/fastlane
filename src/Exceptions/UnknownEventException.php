<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class UnknownEventException extends Exception
{
    public static function make(string $event): UnknownEventException
    {
        return new static("Event with name '{$event}' is unknown.");
    }
}
