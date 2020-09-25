<?php

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class ContentBlockNotRegisteredException extends Exception
{
    protected $message = 'Content Block not registered';

    public static function classNotRegistered(string $class): ContentBlockNotRegisteredException
    {
        return new static('Content Block not registered: ' . $class);
    }

    public static function keyNotRegistered(string $key): ContentBlockNotRegisteredException
    {
        return new static('Content Block not registered: ' . $key);
    }
}
