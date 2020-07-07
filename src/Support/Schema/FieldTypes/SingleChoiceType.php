<?php

namespace CbtechLtd\Fastlane\Support\Schema\FieldTypes;

use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Config\SingleChoiceOption;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class SingleChoiceType extends BaseType
{
    protected array $options;

    static public function make(string $name, string $label, array $options = []): self
    {
        Assert::allIsInstanceOf(
            $options,
            SingleChoiceOption::class,
            'All options must be instances of ' . SingleChoiceOption::class
        );

        $instance = new static($name, $label);
        $instance->options = $options;
        return $instance;
    }

    public function getType(): string
    {
        return 'singleChoice';
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
            fn(SingleChoiceOption $option) => $option->getValue()
        );

        return 'in:' . $values->implode(',');
    }

    public function getConfig(): array
    {
        return [
            'options' => $this->getOptions(),
        ];
    }

    public function toMigration(): string
    {
        $base = "string('{$this->getName()}')";

        if (! $this->isRequired()) {
            $base = "{$base}->nullable()";
        }

        if ($this->hasUniqueRule()) {
            $base = "{$base}->unique()";
        }

        return $base;
    }
}
