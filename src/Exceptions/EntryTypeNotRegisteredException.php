<?php

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class EntryTypeNotRegisteredException extends Exception
{
    protected $message = 'Entry Type not registered';

    public static function classNotRegistered(string $class): EntryTypeNotRegisteredException
    {
        return new static('Entry Type not registered: ' . $class);
    }

    public static function keyNotRegistered(string $key): EntryTypeNotRegisteredException
    {
        return new static('Entry Type not registered: ' . $key);
    }
}
