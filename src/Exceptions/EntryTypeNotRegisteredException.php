<?php

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class EntryTypeNotRegisteredException extends Exception
{
    protected $message = 'Entry Type not registered';
}
