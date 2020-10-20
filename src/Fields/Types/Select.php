<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Select extends Field
{
    protected string $component = 'select';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'multiple' => false,
            'taggable' => false,
            'type'     => 'select',
            'options'  => SelectOptionCollection::make(),
        ]);
    }

    /**
     * Set the frontend field to be rendered as checkboxes for
     * multiple value fields and as radios for single value fields.
     *
     * @return $this
     */
    public function asCheckboxes(): self
    {
        return $this->setConfig('type', 'checkbox');
    }

    /**
     * Set whether the field accepts multiple values.
     *
     * @param bool $state
     * @return $this
     */
    public function multiple(bool $state = true): self
    {
        return $this->setConfig('multiple', $state);
    }

    /**
     * Determine whether the field accepts multiple values.
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->getConfig('multiple');
    }

    /**
     * Enable tags in the select input.
     *
     * @return $this
     */
    public function taggable(): self
    {
        return $this->setConfig('taggable', true);
    }

    /**
     * Determine whether the select input is taggable.
     *
     * @return bool
     */
    public function isTaggable(): bool
    {
        return $this->getConfig('taggable');
    }

    /**
     * Set the available options.
     *
     * @param SelectOptionCollection $collection
     * @return $this
     */
    public function withOptions(SelectOptionCollection $collection): self
    {
        return $this->setConfig('options', $collection);
    }

    public function getOptions(): SelectOptionCollection
    {
        return $this->getConfig('options');
    }

    protected function processReadValue($value, ?EntryType $entryType = null)
    {
        if ($this->isMultiple()) {
            $value = Arr::wrap(is_array($value) ? $value : \json_decode($value));
        }

        return SelectOptionCollection::make(
            Collection::make($value)->map(fn($value) => SelectOption::make($value, $value))->all()
        );
    }

    protected function processWriteValue($value, ?EntryType $entryType = null)
    {
        if ($this->isMultiple()) {
            $value = \json_encode(Arr::wrap($value));
        }

        return $value;
    }
}
