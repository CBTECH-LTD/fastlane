<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryViewModel;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Faker\Provider\ar_SA\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * @property FieldCollection $fields
 * @property string action
 */
class EntryForm extends ReactiveComponent
{
    public EntryInstance $entry;
    public string $formId;
    public array $actions = [];
    public array $data;

    protected $listeners = [
        'fastlane::fieldUpdated' => 'fieldUpdated',
    ];

    /**
     * These rules are needed because Livewire requires
     * that rules must be defined for public models.
     * It's weird, but otherwise it does not work.
     *
     * @var \string[][]
     */
    protected $rules = [
        'entry.entry_type' => ['required'],
        'entry.entry_id' => ['required'],
        'entry.for' => ['required'],
    ];

    /**
     * Mount the Livewire component.
     *
     * @param string        $formId
     * @param EntryInstance $entry
     */
    public function mount(string $formId, EntryInstance $entry)
    {
        $this->formId = $formId;
        $this->entry = $entry;

        $this->data = $this->fields->getData($this->entry->model(), $this->entry->type());
    }

    /**
     * Save the data into the model.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function submit()
    {
        $this->authorizeSubmit();
        $this->fillAndSaveModel();

        return Redirect::to($this->entry->links()->get('self'));
    }

    public function fieldUpdated(array $payload)
    {
        $this->data[$payload['attribute']] = $payload['value'];
    }

    public function render()
    {
        return view('fastlane::components.livewire.form', [
            'entry' => $this->entry,
        ]);
    }

    public function getTitleProperty(): string
    {
        return $this->entry->title();
    }

    public function getFieldsProperty(): FieldCollection
    {
        return $this->entry->fields();
    }

    public function getMetaProperty(): array
    {
        return [
            //
        ];
    }

    public function getActionProperty(): string
    {
        return ($this->entry->exists())
            ? 'update'
            : 'create';
    }

    protected function authorizeSubmit(): void
    {
        if (Gate::getPolicyFor($this->entry->model())) {
            Gate::forUser(Fastlane::user())->authorize($this->action, $this->entry->id());
        }
    }

    protected function fillAndSaveModel(): void
    {
        $message = ($this->action === 'create')
            ? __('fastlane::core.flash.created', ['name' => $this->entry->type()::label()['singular']])
            : __('fastlane::core.flash.updated', ['name' => $this->entry->type()::label()['singular']]);

        $this->entry->commit($this->data);

        Fastlane::flashSuccess($message, 'thumbs-up', $this);
    }
}
