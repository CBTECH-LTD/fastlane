<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use Illuminate\Contracts\Support\Arrayable;

class FieldValue implements Arrayable
{
    private string $name;
    private $value;

    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    public function toArray()
    {
        return [
            $this->name => $this->value,
        ];
    }
}
