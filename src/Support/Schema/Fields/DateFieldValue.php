<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use Carbon\Carbon;

class DateFieldValue extends FieldValue
{
    public function format(string $format): string
    {
        if (!$this->value) {
            return '';
        }

        return Carbon::make($this->value)->format($format);
    }
}
