<?php

namespace CbtechLtd\Fastlane\Support\Schema\Contracts;

use CbtechLtd\Fastlane\Support\Schema\EntrySchemaDefinition;

interface EntrySchema
{
    public function getDefinition(): EntrySchemaDefinition;
}
