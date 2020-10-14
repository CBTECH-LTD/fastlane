<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;

class Date extends Field
{
    protected string $component = 'date';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'displayFormat' => null,
            'time'          => false,
            'seconds'       => false,
        ]);
    }

    public function displayFormat(string $format): self
    {
        return $this->setConfig('displayFormat', $format);
    }

    public function withTime(): self
    {
        return $this->setConfig('time', true);
    }

    public function withSeconds(): self
    {
        return $this->setConfig('seconds', true);
    }

    public function toArray()
    {
        $data = parent::toArray();

        data_set($data, 'config.momentDisplayFormat', $this->convertToMomentJs($this->getDisplayFormat()));

        return $data;
    }

    public function getDisplayFormat()
    {
        if ($displayFormat = $this->getConfig('displayFormat')) {
            return $displayFormat;
        }

        if (! $this->getConfig('time')) {
            return 'd/m/Y';
        }

        if (! $this->getConfig('seconds')) {
            return 'd/m/Y - H:i';
        }

        return 'd/m/Y - H:i:S';
    }

    protected function getSaveFormat(): string
    {
        return $this->getConfig('time')
            ? 'Y-m-d H:i:s'
            : 'Y-m-d';
    }

    protected function getFieldRules(array $data, EntryType $entryType): array
    {
        return [
            $this->getAttribute() => 'date_format:' . $this->getSaveFormat(),
        ];
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
