<?php

namespace CbtechLtd\Fastlane\Fields;

abstract class AbstractExpandedField
{
    protected Field $field;
    protected $rawValue;

    public function __construct(Field $field, $value)
    {
        $this->field = $field;
        $this->rawValue = $value;
    }
}
