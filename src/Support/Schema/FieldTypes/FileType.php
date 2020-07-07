<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

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

    public function toMigration(): string
    {
        $base = "string('{$this->getName()}')";

        if (! $this->isRequired()) {
            $base = "{$base}->nullable()";
        }

        if ($this->hasUniqueRule()) {
            $base = "{$base}->unique()";
        }

        return $base;
    }
}
