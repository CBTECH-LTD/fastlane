<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

class BlockEditor extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.block-editor', [
            'blocks' => [],
            'availableBlocks' => $this->field->getBlocks(),
            'value' => $this->value,
        ]);
    }
}
