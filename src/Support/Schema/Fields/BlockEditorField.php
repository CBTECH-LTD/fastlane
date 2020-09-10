<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class BlockEditorField extends RichEditorField
{
    protected $default = [];

    public function getType(): string
    {
        return 'blockEditor';
    }

    protected function getMigrationMethod(): array
    {
        return ['json'];
    }

    public function toModelAttribute(): array
    {
        return [
            $this->getName() => 'array',
        ];
    }

    protected function buildFieldValueInstance(string $fieldName, $value): FieldValue
    {
        return new BlockFieldValue($fieldName, $value);
    }
}
