<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use Illuminate\Database\Schema\Blueprint;

class ToggleType extends BaseType
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function runOnMigration(Blueprint $table): void
    {
        $col = $table->boolean($this->getName())->nullable(! $this->isRequired());

        if (! $this->hasUniqueRule()) {
            $col->unique();
        }
    }

    protected function getTypeRules(): string
    {
        return 'boolean';
    }
}
