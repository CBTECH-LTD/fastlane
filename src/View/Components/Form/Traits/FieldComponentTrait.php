<?php

namespace CbtechLtd\Fastlane\View\Components\Form\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait FieldComponentTrait
{
    protected string $view;
    public Model $model;
    public string $entryType;
    public string $attribute;
    public $value;

    /**
     * Generate the HTML tag used to identify this component.
     *
     * @return string
     */
    public static function tag(): string
    {
        $name = (new \ReflectionClass(static::class))->getShortName();

        return 'fl-form-' . Str::snake($name, '-');
    }

    /**
     * Render the component.
     *
     * @return \Closure|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function render()
    {
        return view($this->view, array_merge($this->viewData(), [
            'field' => $this->field,
        ]));
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

    /**
     * Initialize the value of the field.
     *
     * @return mixed
     */
    protected function initializeValue()
    {
        return old($this->field->getAttribute(), $this->readValue());
    }

    /**
     * Read the value of the field.
     *
     * @return mixed
     */
    protected function readValue()
    {
        return $this->field->read($this->model, $this->entryType);
    }
}
