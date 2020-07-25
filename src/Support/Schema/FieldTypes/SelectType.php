<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Config\SelectOption;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SelectType extends BaseType
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
}
