<?php

namespace CbtechLtd\Fastlane\Support\ControlPanelResources;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithValue;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EntryResource extends ResourceType
{
    protected EntryType $entryType;
    protected string $destination;

    public function __construct(Model $model, EntryType $entryType)
    {
        parent::__construct($model);
        $this->entryType = $entryType;
    }

    public function type(): string
    {
        return 'fastlane-entry';
    }

    public function transform(): ApiResource
    {
        return new ApiResource($this);
    }

    public function toIndex(): self
    {
        $this->destination = 'index';
        return $this;
    }

    public function toCreate(): self
    {
        $this->destination = 'create';
        return $this;
    }

    public function toUpdate(): self
    {
        $this->destination = 'update';
        return $this;
    }

    public function attributes(Request $request): array
    {
        $fields = $this->getSchemaFields();

        return Collection::make($fields)
            ->mapWithKeys(
                fn(WithValue $field) => $field->resolveValue($this->model)
            )->all();
    }

    protected function meta(): array
    {
        return [
            ResourceMeta::make('item_label', $this->entryType->makeModelTitle($this->model)),
            ResourceMeta::make('entry_type', [
                'schema'        => Collection::make($this->getSchemaFields()),
                'panels'        => Collection::make($this->getSchemaPanels()),
                'singular_name' => $this->entryType->name(),
                'plural_name'   => $this->entryType->pluralName(),
                'identifier'    => $this->entryType->identifier(),
                'icon'          => $this->entryType->icon(),
            ]),
        ];
    }

    protected function links(): array
    {
        return [
            ResourceLink::make('self', ["cp.{$this->entryType->identifier()}.edit", $this->model]),
            ResourceLink::make('parent', ["cp.{$this->entryType->identifier()}.index"]),
        ];
    }

    private function getSchemaFields()
    {
        if ($this->destination === 'update') {
            return $this->entryType->schema()->getUpdateFields();
        }

        if ($this->destination === 'create') {
            return $this->entryType->schema()->getCreateFields();
        }

        return $this->entryType->schema()->getIndexFields();
    }

    private function getSchemaPanels()
    {
        if ($this->destination === 'index') {
            return [];
        }

        return $this->entryType->schema()->getPanels();
    }
}
