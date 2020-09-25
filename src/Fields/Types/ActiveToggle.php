<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

class ActiveToggle extends Boolean
{
    public function __construct(string $label = 'Active', string $attribute = 'is_active')
    {
        parent::__construct($label, $attribute);
    }
}
