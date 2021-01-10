<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Textarea extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.textarea', [
            'value' => $this->value,
        ]);
    }
}
