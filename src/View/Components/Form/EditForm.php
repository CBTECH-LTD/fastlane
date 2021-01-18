<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryViewModel;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class EditForm extends ReactiveComponent
{
    public string $entryType;
    public Model $model;
    public array $actions = [];
    public string $formId;
    public array $data;

    protected $listeners = ['fieldUpdated'];

    public function mount(string $entryType, Model $model, string $formId)
    {
        $this->entryType = $entryType;
        $this->model = $model;
        $this->data = $this->fields->onUpdate()->getData($this->model, $this->entryType);
    }

    public function submit(): void
    {
        if (Gate::getPolicyFor($this->model)) {
            Gate::forUser(Fastlane::user())->authorize('update', $this->model);
        }

        $this->entryType::repository()->update($this->model->getKey(), $this->data);

        Fastlane::flashSuccess(
            __('fastlane::core.flash.updated', ['name' => $this->entryType::label()['singular']]),
            'thumbs-up',
            $this
        );
    }

    public function fieldUpdated(array $payload)
    {
        $this->data[$payload['attribute']] = $payload['value'];
    }

    public function render()
    {
        dd($this->fields);

        $viewModel = $this->buildViewModel()->toArray();

        return view('fastlane::components.livewire.edit-form', $viewModel);
    }

    public function getFieldsProperty(): FieldCollection
    {
        return new FieldCollection($this->entryType::fields());
    }

    protected function buildViewModel(): EntryViewModel
    {
        return new EntryViewModel($this->entryType, $this->fields, $this->model);
    }
}
