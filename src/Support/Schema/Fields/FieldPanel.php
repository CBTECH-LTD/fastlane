<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Makeable;

class FieldPanel implements SchemaField
{
    use Makeable;

    protected string $label;
    protected array $fields;
    protected string $icon = '';

    public function __construct(string $label, array $fields = [])
    {
        $this->label = $label;
        $this->fields = $fields;
    }

    public function getType(): string
    {
        return 'panel';
    }

    public function getName(): string
    {
        return $this->label;
    }

    public function withIcon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function withFields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function toArray()
    {
        return [
            'type'  => $this->getType(),
            'name'  => $this->label,
            'label' => $this->label,
            'icon'  => $this->icon,
        ];
    }
}
