<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Hooks;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Database\Eloquent\Model;

class OnFillingHook
{
    private SchemaField $field;
    private Model $model;
    private $value;

    public function __construct(SchemaField $field, Model $model, $value)
    {
        $this->field = $field;
        $this->model = $model;
        $this->value = $value;
    }

    public function field(): SchemaField
    {
        return $this->field;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
