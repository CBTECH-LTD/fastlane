<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\View\Components\Component;
use CbtechLtd\Fastlane\View\Components\Form\Traits\FieldComponentTrait;
use Illuminate\Database\Eloquent\Model;

abstract class FieldComponent extends Component
{
    use FieldComponentTrait;

    public Field $field;

    /**
     * FieldComponent constructor.
     *
     * @param Model  $model
     * @param string $entryType
     * @param string $attribute
     */
    public function __construct(Model $model, string $entryType, Field $field)
    {
        $this->field = $field;
        $this->attribute = $field->getAttribute();
        $this->model = $model;
        $this->entryType = $entryType;
        $this->value = $this->initializeValue();
    }

    /**
     * Prepare the data provided to the component view.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [];
    }
}
