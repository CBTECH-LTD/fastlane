<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class DateField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected ?string $displayFormat = null;
    protected bool $enableTime = false;
    protected bool $enableSeconds = false;

    public function getType(): string
    {
        return 'date';
    }

    public function displayFormat(string $format): self
    {
        $this->displayFormat = $format;
        return $this;
    }

    public function enableTime(bool $state = true): self
    {
        $this->enableTime = $state;
        return $this;
    }

    public function enableSeconds(bool $state = true): self
    {
        $this->enableSeconds = $state;
        return $this;
    }

    public function getDisplayFormat()
    {
        if ($this->displayFormat) {
            return $this->displayFormat;
        }

        if (! $this->enableTime) {
            return 'DD/MM/Y';
        }

        if (! $this->enableSeconds) {
            return 'DD/MM/Y - H:i';
        }

        return 'DD/MM/Y - H:i:S';
    }

    public function toModelAttribute(): array
    {
        [$type, $format] = $this->enableTime
            ? ['datetime', $this->getSaveFormat()]
            : ['date', $this->getSaveFormat()];

        return [
            $this->getName() => "{$type}:{$format}",
        ];
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'date_format:' . $this->getSaveFormat(),
        ];
    }

    protected function getSaveFormat(): string
    {
        return $this->enableTime
            ? 'Y-m-d H:i:s'
            : 'Y-m-d';
    }

    protected function getMigrationMethod(): array
    {
        return $this->enableTime
            ? ['dateTime']
            : ['date'];
    }

    protected function resolveConfig(EntryInstanceContract $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'displayFormat' => $this->getDisplayFormat(),
            'enableTime'    => $this->enableTime,
            'enableSeconds' => $this->enableSeconds,
        ]);
    }
}
