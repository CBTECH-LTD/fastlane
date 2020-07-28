<?php

namespace CbtechLtd\Fastlane\Support\ApiResources;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
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

    public function toIndex(): self
    {
        $this->destination = 'toIndex';
        return $this;
    }

    public function toCreate(): self
    {
        $this->destination = 'toCreate';
        return $this;
    }

    public function toUpdate(): self
    {
        $this->destination = 'toUpdate';
        return $this;
    }

    public function attributes(Request $request): array
    {
        $fields = $this->getSchemaFields();

        return Collection::make($fields)
            ->mapWithKeys(
                fn(SchemaField $field) => $field->readValue($this->model)
            )->all();
    }

    protected function meta(): array
    {
        return [
            ResourceMeta::make('item_label', $this->entryType->transformModelToString($this->model)),
            ResourceMeta::make('entry_type', [
                'schema'        => Collection::make($this->getSchemaFields()),
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

    /**
     * @return mixed
     */
    private function getSchemaFields()
    {
        return $this->entryType->schema()->{$this->destination}();
    }
}
