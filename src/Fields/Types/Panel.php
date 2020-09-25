<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;
use Illuminate\Support\Collection;

class Panel extends Field
{
    protected string $component = 'panel';

    public function __construct(string $label, ?string $attribute = null)
    {
        parent::__construct($label, $attribute);

        $this->mergeConfig([
            'icon'   => '',
            'fields' => [],
        ]);

        $this->visibility->put('listing', true);
    }

    public function withFields(array $fields): self
    {
        return $this->setConfig('fields', Collection::make($fields));
    }

    public function getFields(): array
    {
        return $this->getConfig('fields')->map(function (Field $field) {
            return $field->inPanel($this);
        })->all();
    }

    public function withIcon(string $icon): self
    {
        return $this->setConfig('icon', $icon);
    }

    public function toArray()
    {
        return [
            'attribute' => $this->getAttribute(),
            'component' => $this->component,
            'config'    => $this->config->except('fields')->toArray(),
        ];
    }

}
