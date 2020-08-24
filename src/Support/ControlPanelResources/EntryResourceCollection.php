<?php

namespace CbtechLtd\Fastlane\Support\ControlPanelResources;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceTypeCollection;
use Illuminate\Support\Collection;

class EntryResourceCollection extends ResourceTypeCollection
{
    protected EntryType $entryType;

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
        $dummyInstance = $this->entryType->newInstance($this->entryType->newModelInstance());

        return [
            ResourceMeta::make('entry_type', [
                'schema'        => Collection::make($dummyInstance->schema()->getIndexFields()),
                'singular_name' => $this->entryType->name(),
                'plural_name'   => $this->entryType->pluralName(),
                'identifier'    => $this->entryType->identifier(),
                'icon'          => $this->entryType->icon(),
            ]),
        ];
    }
}
