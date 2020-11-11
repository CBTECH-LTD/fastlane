<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use Illuminate\Support\Str;

class ActiveToggle extends Boolean
{
    public function __construct(string $label = 'Active', string $attribute = 'is_active')
    {
        parent::__construct($label, $attribute);

        $this->setText(
            Str::upper(__('fastlane::core.fields.active')),
            Str::upper(__('fastlane::core.fields.inactive'))
        );
    }
}
