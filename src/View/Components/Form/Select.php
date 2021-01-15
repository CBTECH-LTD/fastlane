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
        $value = Arr::wrap($this->value);
        $options = $this->field->getOptions();

        if ($this->field->isTaggable()) {
            $options->withTags($value);
        }

        return $options->select($value);
    }
}
