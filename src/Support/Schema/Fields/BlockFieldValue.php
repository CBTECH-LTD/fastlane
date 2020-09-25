<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\ContentBlocks\ContentBlock;
use Illuminate\Support\Collection;

class BlockFieldValue extends FieldValue
{
    public function __construct(string $name, $value)
    {
        parent::__construct($name, $this->prepareValue($value ?? []));
    }

    public function get(string $data, $default = null)
    {
        $parts = explode('.', $data);

        if (! $block = $this->block(array_shift($parts))) {
            return null;
        }

        if (empty($parts)) {
            return $block;
        }

        return $block->field(array_shift($parts))->getValue();
    }

    public function blocks(): Collection
    {
        return $this->value;
    }

    /**
     * Retrieve a given block.
     *
     * @param string $id
     * @return ContentBlock|null
     */
    public function block(string $id): ?ContentBlock
    {
        return $this->value->get($id);
    }

    public function __toString()
    {
        return '';
    }

    public function toArray()
    {
        return [
            $this->name() => $this->value->values()->toArray(),
        ];
    }

    protected function prepareValue(array $value): Collection
    {
        return Collection::make($value)->mapWithKeys(function (array $block) {
            if (! $instance = app('fastlane')->contentBlocks()->get($block['block'])) {
                return null;
            }

            return [
                $block['data']['id'] => $instance::make()->withValues($block),
            ];
        })->filter();
    }
}
