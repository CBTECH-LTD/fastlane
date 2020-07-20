<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

class DateType extends BaseType
{
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

    public function toModelAttribute()
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

    protected function getTypeRules(): string
    {
        return 'date_format:' . $this->getSaveFormat();
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
