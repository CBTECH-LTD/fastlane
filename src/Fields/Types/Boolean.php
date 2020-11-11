<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;

class Boolean extends Field
{
    protected string $formComponent = \CbtechLtd\Fastlane\View\Components\Form\Toggle::class;
    protected string $listingComponent = \CbtechLtd\Fastlane\View\Components\Listing\Boolean::class;

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'default'    => true,
            'text_true'  => fn() => $this->getLabel() . ' - YES',
            'text_false' => fn() => $this->getLabel() . ' - NO',
        ]);
    }

    public function setText(string $true, string $false): self
    {
        return $this
            ->setConfig('text_true', fn() => $true)
            ->setConfig('text_false', fn() => $false);
    }

    public function getTextTrue(): string
    {
        return $this->getConfig('text_true')();
    }

    public function getTextFalse(): string
    {
        return $this->getConfig('text_false')();
    }

    protected function getFieldRules(array $data): array
    {
        return [$this->getAttribute() => 'boolean'];
    }

    protected function processReadValue($value, string $entryType)
    {
        return (bool)$value;
    }

    protected function processWriteValue($value, ?EntryType $entryType = null)
    {
        return (bool)$value;
    }

    public function castUsing()
    {
        return 'boolean';
    }
}
