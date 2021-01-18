<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Toggle extends FieldComponent
{
    protected string $view = 'fastlane::components.form.toggle';

    protected function readValue()
    {
        return (bool) $this->field->read($this->model, $this->entryType);
    }
}
