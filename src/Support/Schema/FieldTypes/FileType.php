<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use Illuminate\Database\Schema\Blueprint;

class FileType extends BaseType
{
    protected array $accept = [];

    public function getType(): string
    {
        return 'file';
    }

    public function getAccept(): array
    {
        return $this->accept;
    }

    public function accept(array $accept): self
    {
        $this->accept = $accept;
        return $this;
    }

    protected function getTypeRules(): string
    {
        return 'file';
    }

    public function runOnMigration(Blueprint $table): void
    {
        $col = $table->string($this->getName())->nullable(! $this->isRequired());

        if ($this->hasUniqueRule()) {
            $col->unique();
        }
    }
}
