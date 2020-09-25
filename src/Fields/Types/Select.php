<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\Transformer;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use CbtechLtd\Fastlane\Fields\Transformers\SelectTransformer;
use Illuminate\Support\Collection;

class Select extends Field
{
    protected string $component = 'select';

    protected Collection $resolvedOptions;

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'multiple' => false,
            'type'     => 'select',
            'options'  => SelectOptionCollection::make(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function transformer(): Transformer
    {
        return new SelectTransformer($this);
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
}
