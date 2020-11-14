<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use Illuminate\Support\Arr;

class Select extends FormComponent
{
    public function render()
    {
        return view('fastlane::components.form.select', [
            'value'   => $this->value,
            'options' => $this->prepareOptions()->collection(),
        ]);
    }

    protected function prepareOptions(): SelectOptionCollection
    {
        return $this->field->getOptions()->select(Arr::wrap($this->value));
    }
}
