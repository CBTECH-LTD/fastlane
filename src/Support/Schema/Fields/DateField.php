<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Sortable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;

class DateField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected ?string $displayFormat = null;
    protected bool $enableTime = false;
    protected bool $enableSeconds = false;

    protected int $listWidth = 180;

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
            'displayFormat'       => $this->getDisplayFormat(),
            'momentDisplayFormat' => $this->convertToMomentJs($this->getDisplayFormat()),
            'enableTime'          => $this->enableTime,
            'enableSeconds'       => $this->enableSeconds,
        ]);
    }

    protected function buildFieldValueInstance(string $fieldName, $value): FieldValue
    {
        return new DateFieldValue($fieldName, $value);
    }

    protected function convertToMomentJs(string $format): string
    {
        $replacements = [
            'A' => 'A',      // for the sake of escaping below
            'a' => 'a',      // for the sake of escaping below
            'B' => '',       // Swatch internet time (.beats), no equivalent
            'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', // ISO 8601
            'D' => 'ddd',
            'd' => 'DD',
            'e' => 'zz',     // deprecated since version 1.6.0 of moment.js
            'F' => 'MMMM',
            'G' => 'H',
            'g' => 'h',
            'H' => 'HH',
            'h' => 'hh',
            'I' => '',       // Daylight Saving Time? => moment().isDST();
            'i' => 'mm',
            'j' => 'D',
            'L' => '',       // Leap year? => moment().isLeapYear();
            'l' => 'dddd',
            'M' => 'MMM',
            'm' => 'MM',
            'N' => 'E',
            'n' => 'M',
            'O' => 'ZZ',
            'o' => 'YYYY',
            'P' => 'Z',
            'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822
            'S' => 'o',
            's' => 'ss',
            'T' => 'z',      // deprecated since version 1.6.0 of moment.js
            't' => '',       // days in the month => moment().daysInMonth();
            'U' => 'X',
            'u' => 'SSSSSS', // microseconds
            'v' => 'SSS',    // milliseconds (from PHP 7.0.0)
            'W' => 'W',      // for the sake of escaping below
            'w' => 'e',
            'Y' => 'YYYY',
            'y' => 'YY',
            'Z' => '',       // time zone offset in minutes => moment().zone();
            'z' => 'DDD',
        ];

        // Converts escaped characters.
        foreach ($replacements as $from => $to) {
            $replacements['\\' . $from] = '[' . $from . ']';
        }

        return strtr($format, $replacements);
    }
}
