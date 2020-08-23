<?php

namespace CbtechLtd\Fastlane\Support\ControlPanelResources;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ReadValue;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EntryResource extends ResourceType
{
    protected EntryInstance $entryInstance;
    protected string $destination;

    public function __construct(EntryInstance $entryInstance)
    {
        parent::__construct($entryInstance->model());
        $this->entryInstance = $entryInstance;
    }

    public function type(): string
    {
        return 'fastlane-entry';
    }

    public function id(): string
    {
        return $this->getModel()->getRouteKey() ?? '';
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
            ->filter(fn($field) => $field instanceof ReadValue)
            ->mapWithKeys(
                fn(ReadValue $field) => $field->readValue($this->entryInstance)->toArray()
            )->all();
    }

    protected function meta(): array
    {
        return [
            ResourceMeta::make('item_label', $this->entryInstance->title()),
            ResourceMeta::make('entry_type', [
                'schema'        => Collection::make($this->getSchemaFields()),
                'panels'        => Collection::make($this->getSchemaPanels()),
                'singular_name' => $this->entryInstance->type()->name(),
                'plural_name'   => $this->entryInstance->type()->pluralName(),
                'identifier'    => $this->entryInstance->type()->identifier(),
                'icon'          => $this->entryInstance->type()->icon(),
            ]),
        ];
    }

    protected function links(): array
    {
        $links = [
            ResourceLink::make('parent', ["cp.{$this->entryInstance->type()->identifier()}.index"]),
        ];

        if ($this->entryInstance->model()->exists) {
            $links[] = ResourceLink::make('self', ["cp.{$this->entryInstance->type()->identifier()}.edit", $this->model]);
        }

        return $links;
    }

    private function getSchemaFields()
    {
        if ($this->destination === 'update') {
            return $this->entryInstance->schema()->getUpdateFields();
        }

        if ($this->destination === 'create') {
            return $this->entryInstance->schema()->getCreateFields();
        }

        return $this->entryInstance->schema()->getIndexFields();
    }

    private function getSchemaPanels()
    {
        if ($this->destination === 'index') {
            return [];
        }

        return $this->entryInstance->schema()->getPanels();
    }
}
