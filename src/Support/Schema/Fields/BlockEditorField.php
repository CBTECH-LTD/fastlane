<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use Illuminate\Support\Collection;

class BlockEditorField extends RichEditorField
{
    protected $default = [];
    protected Collection $blocks;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);

        $this->blocks = app('fastlane')->contentBlocks();
    }

    public function getType(): string
    {
        return 'blockEditor';
    }

    public function disableBlocks(array $blockClasses): self
    {
        foreach ($blockClasses as $class) {
            $this->blocks->remove($class);
        }

        return $this;
    }

    public function withBlocks(array $blockClasses): self
    {
        $this->blocks = app('fastlane')->contentBlocks()->filter(
            fn(string $block) => in_array($block, $blockClasses)
        );

        return $this;
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

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig->put('blocks', $this->blocks->mapWithKeys(
            fn($blockClass) => [$blockClass::key() => $blockClass::make()->forDestination($destination)]
        )->toArray());
    }
}
