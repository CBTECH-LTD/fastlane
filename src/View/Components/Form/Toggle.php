<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Toggle extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.toggle', [
            'value' => (bool)$this->value,
        ]);
    }
}
