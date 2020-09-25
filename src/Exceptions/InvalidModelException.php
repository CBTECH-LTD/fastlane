<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Exceptions;

use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Exception;

class InvalidModelException extends Exception
{
    public static function invalid(string $model): InvalidModelException
    {
        return new static($model . ' is not an instance of ' . BaseModel::class);
    }
}
