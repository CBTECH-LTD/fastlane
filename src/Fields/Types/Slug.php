<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

class Slug extends ShortText
{
    protected string $formComponent = \CbtechLtd\Fastlane\View\Components\Form\Slug::class;
    protected bool $generateFromFieldOnUpdate = false;

    public function generateFromField(string $fieldName, bool $onUpdate = false): self
    {
        return $this->setConfig('baseField', $fieldName);
    }

    public function toArray()
    {
        if (! $this->generateFromFieldOnUpdate) {
            $this->setConfig('baseField', null);
        }

        return parent::toArray();
    }
}
