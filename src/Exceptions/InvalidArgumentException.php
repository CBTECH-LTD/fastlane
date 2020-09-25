<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function notCallable(): InvalidArgumentException
    {
        throw new static('Argument is not callable.');
    }
}
