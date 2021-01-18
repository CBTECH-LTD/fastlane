<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;

class Panel extends FieldComponent
{
    protected string $view = 'fastlane::components.form.panel';

    public function readValue()
    {
        return null;
    }
}
