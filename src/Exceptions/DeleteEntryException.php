<?php

namespace CbtechLtd\Fastlane\Exceptions;

class DeleteEntryException extends \Exception
{
    protected $message = 'Entry model could not be deleted.';
}
