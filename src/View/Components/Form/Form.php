<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;

class Form extends ReactiveComponent
{
    public string $formId;
    public string $method;
    public string $action;
    public FieldCollection $fields;
    public Model $model;
    public string $entryType;

    public function mount(string $formId, string $method, string $action, FieldCollection $fields, Model $model, string $entryType)
    {
        $this->method = $method;
        $this->action = $action;
        $this->formId = $formId;
        $this->fields = $fields;
        $this->model = $model;
        $this->entryType = $entryType;
    }

    public function render()
    {
        return view('fastlane::components.form.form');
    }
}
