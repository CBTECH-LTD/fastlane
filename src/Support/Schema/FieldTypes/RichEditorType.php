<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class RichEditorType extends BaseType
{
    protected bool $acceptFiles = true;

    public function getType(): string
    {
        return 'richEditor';
    }

    public function acceptFiles(bool $state = true): self
    {
        $this->acceptFiles = true;
        return $this;
    }

    public function rejectFiles(): self
    {
        return $this->acceptFiles(false);
    }

    protected function getConfig(): array
    {
        return [
            'acceptFiles' => $this->acceptFiles,
        ];
    }
}
