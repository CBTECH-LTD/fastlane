<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use Illuminate\Database\Schema\Blueprint;

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


    protected function getConfig(): array
    {
        return [
            'displayFormat' => $this->getDisplayFormat(),
            'enableTime'    => $this->enableTime,
            'enableSeconds' => $this->enableSeconds,
        ];
    }

    public function runOnMigration(Blueprint $table): void
    {
        $col = $this->enableTime
            ? $table->dateTime($this->getName())
            : $table->date($this->getName());

        $col->nullable(! $this->isRequired());

        if ($this->hasUniqueRule()) {
            $col->unique();
        }
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
}
