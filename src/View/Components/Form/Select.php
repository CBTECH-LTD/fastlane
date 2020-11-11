<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Select extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.select', [
            'value' => $this->value,
        ]);
    }
}
