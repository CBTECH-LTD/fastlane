<?php

namespace CbtechLtd\Fastlane\Support\Schema\Contracts;

interface EntrySchema
{
    public function build(): EntrySchemaDefinition;
}
