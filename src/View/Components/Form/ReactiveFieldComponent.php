<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\View\Components\Form\Traits\FieldComponentTrait;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;

abstract class ReactiveFieldComponent extends ReactiveComponent
{
    use FieldComponentTrait;

    /**
     * Mount the component.
     *
     * @param Model  $model
     * @param string $entryType
     * @param string $attribute
     */
    public function mount(Model $model, string $entryType, string $attribute)
    {
        $this->attribute = $attribute;
        $this->model = $model;
        $this->entryType = $entryType;
        $this->value = $this->initializeValue();
    }

    /**
     * Get the instance of the field.
     *
     * @return Field
     */
    public function getFieldProperty(): Field
    {
        return (new FieldCollection($this->entryType::fields()))->find($this->attribute);
    }

    public function updatedValue($value)
    {
        $this->emit('fastlane::fieldUpdated', [
            'attribute' => $this->attribute,
            'type' => get_class($this->field),
            'value' => $value,
        ]);
    }
}
