<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

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
            return 'd/m/Y';
        }

        if (! $this->enableSeconds) {
            return 'd/m/Y - H:i';
        }

        return 'd/m/Y - H:i:S';
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

    protected function getConfig(): array
    {
        return [
            'displayFormat' => $this->getDisplayFormat(),
            'enableTime'    => $this->enableTime,
            'enableSeconds' => $this->enableSeconds,
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
}