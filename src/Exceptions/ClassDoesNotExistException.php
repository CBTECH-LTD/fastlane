<?php

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class ClassDoesNotExistException extends Exception
{
    protected $message = 'Class does not exist';

    public static function model(string $class)
    {
        return new static("Model class '{$class}' does not exist");
    }

    public static function apiResource(string $class)
    {
        return new static("Api Resource class '{$class}' does not exist");
    }

    public static function policy(string $class)
    {
        return new static("Policy class '{$class}' does not exist");
    }

    public static function schema(string $class)
    {
        return new static("Schema class '{$class}' does not exist");
    }

    public static function entryType(string $class)
    {
        return new static("EntryType class '{$class}' does not exist");
    }
}
