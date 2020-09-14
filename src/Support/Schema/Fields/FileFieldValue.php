<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use Illuminate\Support\Collection;

class FileFieldValue extends FieldValue
{
    private bool $multiple;

    public function __construct(string $name, $value, bool $multiple)
    {
        parent::__construct($name, $value);
        $this->multiple = $multiple;
    }

    public function items(): Collection
    {
        return Collection::make($this->value());
    }

    public function __toString()
    {
        if (empty($this->value)) {
            return '';
        }

        if (! $this->multiple) {
            return $this->value[0]['url'];
        }

        return Collection::make($this->value)->pluck('url')->implode(', ');
    }
}
