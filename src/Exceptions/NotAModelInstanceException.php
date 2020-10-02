<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class NotAModelInstanceException extends Exception
{
    protected $message = 'The given instance is not a instance of Model';

    public static function model(string $modelClass): self
    {
        return new static('The given instance is not a instance of ' . $modelClass);
    }
}
