<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Toggle extends ReactiveFieldComponent
{
    protected string $view = 'fastlane::components.form.toggle';

    protected function readValue()
    {
        return (bool) parent::readValue();
    }
}
