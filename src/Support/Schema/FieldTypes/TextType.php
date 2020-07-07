<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use Illuminate\Database\Schema\Blueprint;

class TextType extends BaseType
{
    public function getType(): string
    {
        return 'text';
    }

    protected function getTypeRules(): string
    {
        return 'string';
    }

    public function runOnMigration(Blueprint $table): void
    {
        $col = $table->text($this->getName())->nullable(! $this->isRequired());

        if (! $this->hasUniqueRule()) {
            $col->unique();
        }
    }
}
