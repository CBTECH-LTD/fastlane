<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

class Slug extends Field
{
    protected string $component = 'slug';

    public function generateFromField(string $fieldName): self
    {
        return $this->setConfig('baseField', $fieldName);
    }
}
