<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class SingleSelectFieldValue extends FieldValue
{
    public function __construct(string $name, $value)
    {
        parent::__construct($name, array_values($value));
    }

    public function value()
    {
        return data_get($this->value, '0.label');
    }

    public function key(): string
    {
        return data_get($this->value, '0.value');
    }
}
