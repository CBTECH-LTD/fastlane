<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;

class Form extends ReactiveComponent
{
    public string $formId;
    public string $method;
    public string $title;
    public string $subtitle;
    public FieldCollection $fields;
    public Model $model;
    public string $entryType;
    public array $actions = [];

    public function mount(string $title, string $subtitle, string $formId, string $method, FieldCollection $fields, Model $model, string $entryType)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->method = $method;
        $this->formId = $formId;
        $this->fields = $fields;
        $this->model = $model;
        $this->entryType = $entryType;
    }

    public function submit()
    {
        dd('test');
    }

    public function render()
    {
        return view('fastlane::components.form.form');
    }
}
