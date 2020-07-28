<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FieldPanel extends BaseSchemaField
{
    protected array $children = [];

    public function getType(): string
    {
        return 'panel';
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function withChildren(array $children): self
    {
        $this->children = $children;
        return $this;
    }

    public function readValue(Model $model): array
    {
        return $this->resolvedConfig->get('config')['children']->mapWithKeys(
            fn(SchemaField $field) => $field->readValue($model)
        )->all();
    }

    public function hydrateValue($model, $value, EntryRequest $request): void
    {
        $this->getResolvedConfig('config')['children']->each(
            fn(SchemaField $field) => $field->hydrateValue($model, Arr::get($value, $field->getName()), $request)
        );
    }

    protected function resolveConfig(EntryTypeContract $entryType, EntryRequest $request): array
    {
        return [
            'children' => $this->resolveChildren($entryType, $request),
        ];
    }

    protected function resolveChildren(EntryTypeContract $entryType, EntryRequest $request): Collection
    {
        return Collection::make($this->children)->map(
            function (SchemaField $field) use ($entryType, $request) {
                $field->resolve($entryType, $request);
                return $field;
            });
    }
}
