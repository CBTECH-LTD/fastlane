<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

trait RelatesToEntryType
{
    use LoadsAttributesFromEntryType, LoadsRelationsFromEntryType;
}
