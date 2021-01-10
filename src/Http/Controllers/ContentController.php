<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\EntryTypes\Content\ContentEntryType;

class ContentController extends EntryController
{
    protected string $entryType = ContentEntryType::class;
}
