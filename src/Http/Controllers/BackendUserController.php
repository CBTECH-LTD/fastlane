<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;

class BackendUserController extends EntryController
{
    protected string $entryType = BackendUserEntryType::class;
}
