<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\ContentBlocks\ContentBlockCollection;
use CbtechLtd\Fastlane\Contracts\ContentBlockRepository;
use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;
use Illuminate\Support\Collection;

class BlockEditor extends Field
{
    protected string $formComponent = 'blockEditor';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'blocks' => ContentBlockCollection::make(app(ContentBlockRepository::class)->all()),
        ]);
    }

    public function disableBlocks(array $blockClasses): self
    {
        return $this->setConfig('blocks', $this->getConfig('blocks')->filter(
            fn(string $class) => ! in_array($class, $blockClasses)
        ));
    }

    public function withBlocks(array $blockClasses): self
    {
        return $this->setConfig('blocks', Collection::make(app(ContentBlockRepository::class)->all())->filter(
            fn(string $class) => in_array($class, $blockClasses)
        ));
    }

    public function toArray()
    {
        if ($this->arrayFormat === 'listing') {
            $this->getConfig('blocks')->shallow();
        }

        return parent::toArray();
    }

    protected function processReadValue($value, string $entryType)
    {
        $val = is_array($value) ? $value : json_decode($value ?? '[]', true);

        return Collection::make($val)->map(function (array $block) {
            if (! $instance = app(ContentBlockRepository::class)->findByKey($block['block'])) {
                return null;
            }

            return $instance::make()->withValues($block);
        })->filter();
    }

    protected function processWriteValue($value, ?EntryType $entryType = null)
    {
        return json_encode($value);
    }
}