<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class ShortText extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.short-text', [
            'value' => $this->value,
        ]);
    }
}
