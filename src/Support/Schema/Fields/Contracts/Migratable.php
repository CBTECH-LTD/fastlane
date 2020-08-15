<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

interface Migratable
{
    public function toMigration(): string;
}
