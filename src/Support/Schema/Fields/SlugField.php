<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class SlugField extends StringField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected string $baseField = '';

    public function getType(): string
    {
        return 'slug';
    }

    public function makeFromField(string $field): self
    {
        $this->baseField = $field;
        return $this;
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'baseField' => $this->baseField,
        ]);
    }
}
