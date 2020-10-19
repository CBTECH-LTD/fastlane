<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;

class Slug extends Field
{
    protected string $component = 'slug';
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
