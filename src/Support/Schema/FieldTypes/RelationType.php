<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Config\SelectOption;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use Closure;
use Illuminate\Support\Collection;

abstract class RelationType extends BaseType
{
    protected $default = null;
    protected bool $multiple = false;
    protected EntryType $relatedEntryType;
    protected array $options = [];

    abstract public function isMultiple(): bool;

    /**
     * A string representing a function name to be used as relationship.
     *
     * @return string
     */
    abstract public function getRelationshipName(): string;

    /**
     * A closure function to be used as the relationship method (like a
     * normal Laravel relationship method).
     *
     * @return Closure
     */
    abstract public function getRelationshipMethod(): Closure;

    abstract protected function hydrateRelation($model, $value, EntryRequest $request): void;

    public function __construct(string $relatedEntryType, ?string $label = null)
    {
        /** @var EntryType $class */
        $this->relatedEntryType = app()->make($relatedEntryType);

        $this->label = $this->relatedEntryType->pluralName();
        $this->name = "relations__{$this->relatedEntryType->identifier()}";

        $this->options = $this->relatedEntryType->getItems()->getCollection()->map(
            fn(ApiResource $res) => SelectOption::make((int)$res->getModel()->getKey(), $res->getModel()->toString())
        )->all();
    }

    static public function make(string $relatedEntryType, ?string $label = null): self
    {
        return new static($relatedEntryType, $label);
    }

    public function getType(): string
    {
        return 'select';
    }

    public function getRelatedEntryType(): EntryType
    {
        return $this->relatedEntryType;
    }

    public function setRelatedEntryType(EntryType $relatedEntryType): self
    {
        $this->relatedEntryType = $relatedEntryType;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected function getTypeRules(): string
    {
        $values = Collection::make($this->options)->map(
            fn(SelectOption $option) => $option->getValue()
        );

        if ($this->isMultiple()) {
            return 'array';
        }

        return 'in:' . $values->implode(',');
    }

    public function getConfig(): array
    {
        return [
            'options'  => $this->getOptions(),
            'multiple' => $this->isMultiple(),
        ];
    }

    public function hydrateValue($model, $value, EntryRequest $request): void
    {
        if (is_callable($this->hydrateCallback)) {
            call_user_func($this->hydrateCallback, $model, $value, $request);
            return;
        }

        $this->hydrateRelation($model, $value, $request);
    }
}
