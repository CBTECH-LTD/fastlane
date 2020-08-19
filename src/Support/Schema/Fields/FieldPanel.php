<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\Makeable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Panelizable;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FieldPanel implements SchemaField, Resolvable
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
        return Str::snake($this->label);
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
            'name'  => $this->getName(),
            'type'  => $this->getType(),
            'label' => $this->label,
            'icon'  => $this->icon,
        ];
    }

    public function resolve(EntryTypeContract $entryType, array $data): array
    {
        return Collection::make($this->fields)
            ->flatMap(function (SchemaField $field) use ($entryType, $data) {
                if (! $field instanceof Resolvable) {
                    return false;
                }

                if ($field instanceof Panelizable) {
                    $field->inPanel($this);
                }

                return Arr::wrap($field->resolve($entryType, $data));
            })
            ->all();
    }
}
