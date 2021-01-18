<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use Illuminate\Support\Arr;

class Select extends FieldComponent
{
    protected string $view = 'fastlane::components.form.select';

    protected function viewData(): array
    {
        return [
            'options' => $this->prepareOptions()->collection(),
        ];
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
