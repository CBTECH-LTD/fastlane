<?php

namespace CbtechLtd\Fastlane\Support\ApiResources;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceTypeCollection;
use Illuminate\Support\Collection;

class EntryResourceCollection extends ResourceTypeCollection
{
    protected EntryType $entryType;

    public function __construct(array $items = [])
    {
        parent::__construct($items);

        foreach ($this->items as $item) {
            $item->withOptions([
                'output' => 'collection',
            ]);
        }
    }

    public function forEntryType(EntryType $entryType): self
    {
        $this->entryType = $entryType;
        return $this;
    }

    public function resourceType(): string
    {
        return EntryResource::class;
    }

    protected function links(): array
    {
        return [
            ResourceLink::make('create', ["cp.{$this->entryType->identifier()}.create"]),
        ];
    }

    protected function meta(): array
    {
        return [
            ResourceMeta::make('entry_type', [
                'schema'        => Collection::make($this->entryType->schema()->getIndexFields()),
                'singular_name' => $this->entryType->name(),
                'plural_name'   => $this->entryType->pluralName(),
                'identifier'    => $this->entryType->identifier(),
                'icon'          => $this->entryType->icon(),
            ]),
        ];
    }
}
