<?php

namespace CbtechLtd\Fastlane\Fields\Support;

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

    public function select(bool $state = true): self
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
        return [
            'label'    => $this->getLabel(),
            'value'    => $this->getValue(),
            'selected' => $this->selected,
        ];
    }
}
