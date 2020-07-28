<?php

namespace CbtechLtd\Fastlane\Exceptions;

use Exception;

class UnresolvedException extends Exception
{
    protected $message = 'Class is not resolved.';
}
