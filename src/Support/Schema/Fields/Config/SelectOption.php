<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Config;

use Illuminate\Contracts\Support\Arrayable;

class SelectOption implements Arrayable
{
    private string $value;
    private string $label;
    private bool $selected = false;

    public function __construct(string $value, string $label)
    {
        $this->value = $value;
        $this->label = $label;
    }

    public static function make(string $value, string $label): SelectOption
    {
        return new static($value, $label);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function selected(bool $state = true): self
    {
        $this->selected = $state;
        return $this;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function toArray()
    {
        $val = $this->getValue();

        return [
            'label'    => $this->getLabel(),
            'value'    => is_int($val) ? $val : $val,
            'selected' => $this->isSelected(),
        ];
    }
}
