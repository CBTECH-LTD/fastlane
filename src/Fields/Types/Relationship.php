<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use Closure;

abstract class Relationship extends Field
{
    protected string $component = 'select';
    protected EntryType $relatedEntryType;
    protected array $columns = [];

    /**
     * Relationship constructor.
     *
     * @param EntryType|string $relatedEntryType
     * @param mixed            ...$columns
     */
    public function __construct(string $relatedEntryType, ...$columns)
    {
        $this->relatedEntryType = $relatedEntryType::newInstance();
        $this->columns = $columns;

        parent::__construct($this->getRelationshipLabel(), $this->getRelationshipMethod());

        $this->mergeConfig([
            'multiple'   => false,
            'timestamps' => true,
            'options'    => $this->loadOptionsFromRelatedEntryType(),
            'type'       => 'select',
        ]);
    }

    /**
     * Set a custom label to the relationship.
     *
     * @param string $label
     * @return $this
     */
    public function withLabel(string $label): self
    {
        return $this->setConfig('label', $label);
    }

    /**
     * A string representing a function name to be used as relationship.
     *
     * @return string
     */
    abstract public function getRelationshipMethod(): string;

    /**
     * A string representing the label of the field.
     *
     * @return string
     */
    abstract public function getRelationshipLabel(): string;

    /**
     * A closure function to be used as the relationship method (like a
     * normal Laravel relationship method).
     *
     * @return Closure
     */
    abstract public function getRelationshipResolver(): Closure;

    /**
     * Determine whether the relationship accepts multiple items.
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return $this->getConfig('multiple');
    }

    /**
     * Disable timestamps in the pivot data.
     *
     * @return $this
     */
    public function withoutTimestamps(): self
    {
        return $this->setConfig('timestamps', false);
    }

    /**
     * Render options as checkboxes in the control panel.
     *
     * @return $this
     */
    public function asCheckboxes(): self
    {
        return $this->setConfig('asCheckboxes', 'checkbox');
    }

    /**
     * Retrieve the related entry type.
     *
     * @return EntryType
     */
    public function getRelatedEntryType(): EntryType
    {
        return $this->relatedEntryType;
    }

    public function setRelatedEntryType(EntryType $entryType): self
    {
        $this->relatedEntryType = $entryType;
        return $this;
    }

    protected function getFieldRules(array $data, EntryType $entryType): array
    {
        $values = $this->getConfig('options')->load()->collection()->map(
            fn(SelectOption $option) => $option->getValue()
        );

        $inRule = 'in:' . $values->implode(',');

        if ($this->isMultiple()) {
            return [
                $this->getAttribute()       => 'array',
                "{$this->getAttribute()}.*" => $inRule,
            ];
        }

        return [$this->getAttribute() => $inRule];
    }

    protected function buildOptions(): SelectOptionCollection
    {
        return SelectOptionCollection::make();
    }

    /**
     * @return SelectOptionCollection
     */
    protected function loadOptionsFromRelatedEntryType(): SelectOptionCollection
    {
        return SelectOptionCollection::lazy(function () {
            return $this->relatedEntryType::query()
                ->select(['id', ...$this->columns])
                ->get()
                ->map(function (EntryType $entryType) {
                    return SelectOption::make(
                        (string)$entryType->entryKey(),
                        $entryType->entryTitle()
                    );
                })->all();
        });
    }
}
