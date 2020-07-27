<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class RelationField extends BaseSchemaField
{
    protected $default = null;
    protected bool $multiple = false;
    protected bool $withTimestamps = true;
    protected ?Collection $options = null;
    protected EntryType $relatedEntryType;

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
    }

    static public function make(string $relatedEntryType, ?string $label = null): self
    {
        return new static($relatedEntryType, $label);
    }

    public function withTimestamps(bool $state = true): self
    {
        $this->withTimestamps = $state;
        return $this;
    }

    public function withoutTimestamps(): self
    {
        return $this->withTimestamps(false);
    }

    public function readValue(Model $model)
    {
        throw new \Exception('readValue not implemented.');
    }

    public function getType(): string
    {
        return 'select';
    }

    public function hydrateValue($model, $value, EntryRequest $request): void
    {
        if (is_callable($this->hydrateCallback)) {
            call_user_func($this->hydrateCallback, $model, $value, $request);
            return;
        }

        $this->hydrateRelation($model, $value, $request);
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
        return $this->getSelectOptions()->toArray();
    }

    protected function getTypeRules(): array
    {
        $values = Collection::make($this->getSelectOptions())->map(
            fn(SelectOption $option) => $option->getValue()
        );

        $inRule = 'in:' . $values->implode(',');

        if ($this->isMultiple()) {
            return [
                $this->getName() => 'array',
                "{$this->getName()}.*" => $inRule,
            ];
        }

        return [ $this->getName() => 'in:' . $values->implode(',') ];
    }

    public function getConfig(): array
    {
        return [
            'options'  => $this->getOptions(),
            'multiple' => $this->isMultiple(),
        ];
    }

    public function toMigration(): string
    {
        return '';
    }

    protected function getSelectOptions(): Collection
    {
        if (! $this->options) {
            $this->options = $this->relatedEntryType->getItems()->map(function (Model $model) {
                return SelectOption::make(
                    $model->getKey(),
                    $this->entryType->transformModelToString($model)
                );
            });
        }

        return $this->options;
    }
}
