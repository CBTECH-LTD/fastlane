<?php

namespace CbtechLtd\Fastlane\Fields;

class Value
{
    protected Field $field;
    protected $rawValue;

    public function __construct(Field $field, $rawValue)
    {
        $this->field = $field;
        $this->rawValue = $rawValue;
    }

    public function value()
    {
        return $this->raw();
    }

    public function raw()
    {
        return $this->rawValue;
    }
}
