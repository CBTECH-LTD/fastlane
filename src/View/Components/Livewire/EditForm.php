<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Livewire;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryCollectionViewModel;
use CbtechLtd\Fastlane\Http\ViewModels\EntryViewModel;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class EditForm extends ReactiveComponent
{
    public string $entryType;
    public Model $model;
    public array $actions = [];
    public string $formId;
    public array $data;

    public function mount(string $entryType, Model $model)
    {
        $this->entryType = $entryType;
        $this->model = $model;
        $this->formId = Str::uuid()->toString();

        $this->data = $this->getFields()->onUpdate()->getData($this->model, $this->entryType);
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

    public function render()
    {
        $viewModel = $this->buildViewModel()->toArray();

        return view('fastlane::components.livewire.edit-form', $viewModel);
    }

    /**
     * Get fields of the entry type.
     *
     * @return FieldCollection
     */
    protected function getFields(): FieldCollection
    {
        return once(fn () => new FieldCollection($this->entryType::fields()));
    }

    protected function buildViewModel(): EntryViewModel
    {
        $fields = $this->getFields()->onUpdate();

        return new EntryViewModel($this->entryType, $fields, $this->model);
    }
}
