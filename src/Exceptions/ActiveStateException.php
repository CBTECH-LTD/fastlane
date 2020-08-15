<?php

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class ActiveStateException extends Exception
{
    public static function couldNotActivate(): self
    {
        return new ActiveStateException('Registry could not be activated');
    }

    public static function couldNotDeactivate(): self
    {
        return new ActiveStateException('Registry could not be deactivated');
    }
}
