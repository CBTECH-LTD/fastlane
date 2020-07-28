<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SelectField extends BaseSchemaField
{
    protected $default = null;
    protected bool $multiple = false;
    protected $options;

    static public function make(string $name, string $label, array $options = []): self
    {
        Assert::allIsInstanceOf(
            $options,
            SelectOption::class,
            'All options must be instances of ' . SelectOption::class
        );

        $instance = new static($name, $label);
        $instance->options = $options;
        return $instance;
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

    public function readValue(Model $model)
    {
        $value = Arr::wrap($model->{$this->getName()});

        $options = $this->resolvedConfig->get('config')['options'];

        return $options->filter(
            fn(SelectOption $opt) => $opt->isSelected() || in_array($opt->getValue(), $value)
        )->toArray();
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

    protected function resolveConfig(EntryTypeContract $entryType, EntryRequest $request): array
    {
        return [
            'options'  => $this->resolveOptions($request),
            'multiple' => $this->isMultiple(),
        ];
    }

    protected function resolveOptions(EntryRequest $request): Collection
    {
        $options = is_callable($this->options)
            ? call_user_func($this->options, $request)
            : $this->options;

        return Collection::make($options);
    }
}
