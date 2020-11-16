<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\View\Components\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class FormComponent extends Component
{
    public Field $field;
    public Model $model;
    public string $entryType;
    public $value;

    public function __construct(Field $field, Model $model, string $entryType)
    {
        $this->field = $field;
        $this->model = $model;
        $this->entryType = $entryType;
        $this->value = old($this->field->getAttribute(), $this->readValue());
    }

    public function readValue()
    {
        return $this->field->read($this->model, $this->entryType);
    }

    public static function tag(): string
    {
        $name = (new \ReflectionClass(static::class))->getShortName();

        return 'fl-form-' . Str::snake($name, '-');
    }
}
