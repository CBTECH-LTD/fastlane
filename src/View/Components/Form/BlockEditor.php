<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class BlockEditor extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.block-editor', [
            'value' => $this->value,
        ]);
    }
}
