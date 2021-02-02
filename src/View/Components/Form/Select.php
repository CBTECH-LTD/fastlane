<?php

namespace CbtechLtd\Fastlane\View\Components\Form;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use Illuminate\Support\Arr;

class Select extends ReactiveFieldComponent
{
    protected string $view = 'fastlane::components.form.select';
    public array $options = [];
    public bool $optionsLoaded = false;

    public function mount(EntryInstance $entry, string $attribute)
    {
        parent::mount($entry, $attribute);

        if ($this->optionsLoaded = $this->field->getOptions()->isLoaded()) {
            $this->loadOptions();
        }
    }

    public function loadOptions(): void
    {
        if (! $this->optionsLoaded) {
            $this->optionsLoaded = true;
        }

        $this->options = $this->prepareOptions()->collection()->toArray();
    }


    protected function prepareOptions(): SelectOptionCollection
    {
        if (! $this->optionsLoaded) {
            return new SelectOptionCollection;
        }

        $value = Arr::wrap($this->value);
        $options = $this->field->getOptions()->load();

        if ($this->field->isTaggable()) {
            $options->withTags($value);
        }

        return $options->select($value);
    }
}
