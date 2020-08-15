<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SelectField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected $default = null;
    protected bool $multiple = false;
    protected bool $renderAsCheckbox = false;
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

    public function withOptions($options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->getResolvedConfig('config')['options']->toArray();
    }

    public function asCheckboxes(bool $state = true): self
    {
        $this->renderAsCheckbox = $state;
        return $this;
    }

    public function resolveValue(Model $model): array
    {
        $value = Arr::wrap($model->{$this->getName()});

        $options = $this->resolvedConfig->get('config')['options'];

        return [
            $this->getName() => $options->filter(
                fn(SelectOption $opt) => $opt->isSelected() || in_array($opt->getValue(), $value)
            )->toArray(),
        ];
    }

    public function toApiAttribute(Model $model, array $options = [])
    {
        if ($this->toApiAttributeCallback) {
            return call_user_func($this->toApiAttributeCallback, $model);
        }

        return $this->resolveValue($model);
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

    protected function resolveConfig(EntryTypeContract $entryType, array $data): array
    {
        return [
            'options'  => $this->resolveOptions($data),
            'multiple' => $this->isMultiple(),
            'type'     => $this->renderAsCheckbox ? 'checkbox' : 'select',
        ];
    }

    protected function resolveOptions(array $data): Collection
    {
        $options = is_callable($this->options)
            ? call_user_func($this->options, $data)
            : $this->options;

        return Collection::make($options);
    }
}
