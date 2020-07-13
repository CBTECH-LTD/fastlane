<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use Illuminate\Database\Schema\Blueprint;

class ToggleType extends BaseType
{
    protected $default = true;

    public function getType(): string
    {
        return 'toggle';
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
