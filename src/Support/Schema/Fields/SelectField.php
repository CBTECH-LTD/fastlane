<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SelectField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected $default = null;
    protected bool $multiple = false;
    protected bool $taggable = false;
    protected bool $renderAsCheckbox = false;

    /** @var Closure | array */
    protected $options;

    protected function __construct(string $name, string $label, array $options = [])
    {
        parent::__construct($name, $label);

        Assert::allIsInstanceOf(
            $options,
            SelectOption::class,
            'All options must be instances of ' . SelectOption::class
        );

        $this->options = $options;
    }

    public function getType(): string
    {
        return 'select';
    }

    public function multiple(bool $state = true): self
    {
        $this->multiple = $state;
        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function taggable(bool $taggable = true): self
    {
        $this->taggable = $taggable;
        return $this;
    }

    public function isTaggable(): bool
    {
        return $this->taggable;
    }

    public function withOptions($options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->getResolvedConfig('options')->toArray();
    }

    public function asCheckboxes(bool $state = true): self
    {
        $this->renderAsCheckbox = $state;
        return $this;
    }

    public function readValue(EntryInstance $entryInstance): FieldValue
    {
        $value = Arr::wrap($entryInstance->model()->{$this->getName()});

        /** @var Collection<SelectOption> $options */
        $options = $this->resolvedConfig->get('options');

        return $this->buildFieldValueInstance($this->getName(), $options->filter(
            fn(SelectOption $opt) => $opt->isSelected() || in_array($opt->getValue(), $value)
        )->toArray());
    }

    public function toApiAttribute(Model $model, array $options = [])
    {
        if ($this->toApiAttributeCallback) {
            return call_user_func($this->toApiAttributeCallback, $model);
        }

        return $this->readValue($model);
    }

    public function toModelAttribute(): array
    {
        $cast = $this->isMultiple()
            ? 'array'
            : null;

        return [$this->getName() => $cast];
    }

    protected function getTypeRules(): array
    {
        $values = $this->getResolvedConfig('options')->map(
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

    protected function getDefault()
    {
        if ($this->isMultiple()) {
            return [];
        }

        return null;
    }

    protected function getMigrationMethod(): array
    {
        if ($this->isMultiple()) {
            return ['json'];
        }

        return ['string'];
    }

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'multiple' => $this->isMultiple(),
            'taggable' => $this->isTaggable(),
            'type'     => $this->renderAsCheckbox ? 'checkbox' : 'select',
        ]);

        $this->resolveOptions($entryInstance);
    }

    protected function resolveOptions(EntryInstance $entryInstance): void
    {
        $options = is_callable($this->options)
            ? call_user_func($this->options, $entryInstance)
            : $this->options;

        $this->resolvedConfig->put('options', Collection::make($options));
    }

    protected function buildFieldValueInstance(string $fieldName, $value): FieldValue
    {
        if ($this->isMultiple()) {
            return new MultipleSelectFieldValue($fieldName, $value);
        }

        return new SingleSelectFieldValue($fieldName, $value);
    }
}
