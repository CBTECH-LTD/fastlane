<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SelectField extends BaseSchemaField
{
    protected $default = null;
    protected bool $multiple = false;
    protected array $options;

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

    public function getOptions(): array
    {
        return Collection::make($this->options)->toArray();
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
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
            'options' => $this->getOptions(),
            'multiple' => $this->isMultiple(),
        ];
    }

    public function readValue(Model $model)
    {
        $value = Arr::wrap($model->{$this->getName()});

        if (count($value) === 0) {
            return [];
        }

        return Collection::make($this->options)->filter(
            fn(SelectOption $opt) => in_array($opt->getValue(), $value)
        )->toArray();
    }
}
