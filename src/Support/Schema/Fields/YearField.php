<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;

class YearField extends AbstractBaseField
{
    public function getType(): string
    {
        return 'year';
    }

    public function readValue(EntryInstanceContract $entryInstance): FieldValue
    {
        if (is_callable($this->readValueCallback)) {
            return call_user_func($this->readValueCallback, $entryInstance);
        }

        return new FieldValue($this->getName(), $entryInstance->model()->{$this->getName()}->format('Y'));
    }

    public function toModelAttribute(): array
    {
        return [
            $this->getName() => "date:Y",
        ];
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'date_format:Y',
        ];
    }

    protected function getMigrationMethod(): array
    {
        return ['date'];
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'displayFormat' => 'YYYY',
        ]);
    }
}
