<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use Illuminate\Database\Schema\Blueprint;

class DateType extends BaseType
{
    public function getType(): string
    {
        return 'date';
    }

    public function runOnMigration(Blueprint $table): void
    {
        $col = $table
            ->date($this->getName())
            ->nullable(! $this->isRequired());

        if ($this->hasUniqueRule()) {
            $col->unique();
        }
    }
}
