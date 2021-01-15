<?php

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\View\Components\Component;
use Illuminate\Database\Eloquent\Model;

abstract class ListingComponent extends Component
{
    public Field $field;
    public Model $model;
    public string $attribute;
    public $value;

    public function __construct(Field $field, Model $model, string $attribute, $value)
    {
        $this->field = $field;
        $this->model = $model;
        $this->attribute = $attribute;
        $this->value = $this->initiateValue($value);
    }

    protected function initiateValue($value)
    {
        return $value;
    }
}
