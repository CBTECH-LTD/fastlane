<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

use CbtechLtd\Fastlane\Fields\Field;
use Illuminate\Support\Arr;

class Panel extends Field
{
    protected string $formComponent = \CbtechLtd\Fastlane\View\Components\Form\Panel::class;

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
        $fields = FieldCollection::make($fields)->map(function (Field $field) {
            return $field->inPanel($this);
        });

        return $this->setConfig('fields', $fields);
    }

    public function getFields(): FieldCollection
    {
        return $this->getConfig('fields');
    }

    public function withIcon(string $icon): self
    {
        return $this->setConfig('icon', $icon);
    }

    public function getIcon(): string
    {
        return $this->getConfig('icon');
    }

    public function isVisibleOnCreate(): bool
    {
        return parent::isVisibleOnCreate()
            && $this->getFields()->onCreate()->isNotEmpty();
    }

    public function isVisibleOnUpdate(): bool
    {
        return parent::isVisibleOnUpdate()
            && $this->getFields()->onUpdate()->isNotEmpty();
    }

    public function toArray()
    {
        $data = parent::toArray();

        $data['config'] = Arr::except($data['config'], ['fields']);

        return $data;
    }

}
