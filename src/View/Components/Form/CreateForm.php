<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryViewModel;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class CreateForm extends ReactiveComponent
{
    public Model $model;
    public string $entryType;
    public array $actions = [];
    public string $formId;
    public array $data;

    protected $listeners = [
        'fastlane::fieldUpdated' => 'fieldUpdated',
    ];

    public function mount(string $entryType, string $formId)
    {
        $this->entryType = $entryType;
        $this->model = $this->entryType::repository()->getModel();
        $this->data = $this->fields->getData($this->model, $this->entryType);
    }

    public function submit()
    {
        if (Gate::getPolicyFor($this->model)) {
            Gate::authorize('create', $this->model);
        }

        $entry = $this->entryType::repository()->store($this->data);

        Fastlane::flashSuccess(
            __('fastlane::core.flash.created', ['name' => $this->entryType::label()['singular']]),
            'thumbs-up'
        );

        if ($this->entryType::routes()->has('edit')) {
            return Redirect::to($this->entryType::routes()->get('edit')->url($entry));
        }

        if ($this->entryType::routes()->get('index')) {
            return Redirect::to($this->entryType::routes()->get('index')->url());
        }

        return Redirect::back();
    }

    public function fieldUpdated(array $payload)
    {
        $this->data[$payload['attribute']] = $payload['value'];
    }

    public function render()
    {
        $viewModel = $this->buildViewModel()->toArray();

        return view('fastlane::components.livewire.form', $viewModel);
    }

    public function getFieldsProperty(): FieldCollection
    {
        return (new FieldCollection($this->entryType::fields()))->onCreate();
    }

    protected function buildViewModel(): EntryViewModel
    {
        return new EntryViewModel($this->entryType, $this->fields, $this->model);
    }
}
