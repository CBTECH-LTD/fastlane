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
}
