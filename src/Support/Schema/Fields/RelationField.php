<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiRelationship;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiRelationship as ExportsToApiRelationshipContract;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class RelationField extends AbstractBaseField implements ExportsToApiRelationshipContract
{
    use ExportsToApiRelationship;

    protected $default = null;
    protected bool $multiple = false;
    protected bool $withTimestamps = true;
    protected ?Collection $options = null;
    protected bool $renderAsCheckbox = false;
    protected EntryTypeContract $relatedEntryType;

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

    abstract protected function hydrateRelation($model, $value, array $requestData): void;

    public function __construct(string $relatedEntryType, ?string $label = null)
    {
        /** @var EntryTypeContract $class */
        $this->relatedEntryType = app()->make($relatedEntryType);

        $this->label = $this->relatedEntryType->pluralName();
        $this->name = "relations__{$this->relatedEntryType->identifier()}";
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

    public function resolveValue(Model $model): array
    {
        throw new \Exception('readValue not implemented.');
    }

    public function getType(): string
    {
        return 'select';
    }

    public function asCheckboxes(bool $state = true): self
    {
        $this->renderAsCheckbox = $state;
        return $this;
    }

    public function fillModel($model, $value, array $requestData): void
    {
        if (is_callable($this->fillValueCallback)) {
            call_user_func($this->fillValueCallback, $model, $value, $requestData);
            return;
        }

        $this->hydrateRelation($model, $value, $requestData);
    }

    public function getRelatedEntryType(): EntryTypeContract
    {
        return $this->relatedEntryType;
    }

    public function setRelatedEntryType(EntryTypeContract $relatedEntryType): self
    {
        $this->relatedEntryType = $relatedEntryType;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->getResolvedConfig('config')['options']->toArray();
    }

    protected function getTypeRules(): array
    {
        $values = $this->getResolvedConfig('config')['options']->map(
            fn(SelectOption $option) => $option->getValue()
        );

        $inRule = 'in:' . $values->implode(',');

        if ($this->isMultiple()) {
            return [
                $this->getName()       => 'array',
                "{$this->getName()}.*" => $inRule,
            ];
        }

        return [$this->getName() => 'in:' . $values->implode(',')];
    }

    public function toMigration(): string
    {
        return '';
    }

    protected function resolveConfig(EntryTypeContract $entryType, array $data): array
    {
        return [
            'options'  => $this->resolveOptions($entryType, $data),
            'multiple' => $this->isMultiple(),
            'type'     => $this->renderAsCheckbox ? 'checkbox' : 'select',
        ];
    }

    protected function resolveOptions(EntryTypeContract $entryType, array $data): Collection
    {
        return $this->relatedEntryType->getItems()->map(function (Model $model) use ($entryType) {
            return SelectOption::make(
                $model->getKey(),
                $entryType->makeModelTitle($model)
            );
        });
    }
}