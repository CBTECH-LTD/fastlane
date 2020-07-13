<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use Illuminate\Database\Schema\Blueprint;

class StringType extends BaseType
{
    protected $default = '';

    public function getType(): string
    {
        return 'string';
    }

    public function runOnMigration(Blueprint $table): void
    {
        $col = $table->string($this->getName())->nullable(! $this->isRequired());

        if (! $this->hasUniqueRule()) {
            $col->unique();
        }
    }

    protected function getTypeRules(): string
    {
        return 'string';
    }
}
