<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

class ImageFieldValue extends FieldValue
{
    private bool $multiple;

    public function __construct(string $name, $value, bool $multiple)
    {
        parent::__construct($name, $value);
        $this->multiple = $multiple;
    }

    public function value()
    {
        if (! empty($this->value)) {
            dd($this->value);
        }
        
        return '';
    }
}
