<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class SlugField extends StringField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected string $baseField = '';
    protected bool $makeFromFieldOnUpdate = false;

    public function getType(): string
    {
        return 'slug';
    }

    public function makeFromField(string $field, bool $enableOnUpdate = false): self
    {
        $this->baseField = $field;
        $this->makeFromFieldOnUpdate = $enableOnUpdate;
        return $this;
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'baseField' => $this->getBaseField($destination, $entryInstance),
        ]);
    }

    /**
     * @param string                $destination
     * @param EntryInstanceContract $entryInstance
     * @return string|null
     */
    protected function getBaseField(string $destination, EntryInstanceContract $entryInstance): ?string
    {
        if ($destination === 'index') {
            return null;
        }

        if (! $entryInstance->model()->exists) {
            return $this->baseField;
        }

        return ($this->makeFromFieldOnUpdate)
            ? $this->baseField
            : null;
    }
}
