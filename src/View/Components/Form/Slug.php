<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class Slug extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.slug', [
            'value' => $this->value,
        ]);
    }
}
