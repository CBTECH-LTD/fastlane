<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Panel extends FormComponent
{
    public function readValue()
    {
        return null;
    }

    public function render()
    {
        return view('fastlane::components.form.panel');
    }
}
