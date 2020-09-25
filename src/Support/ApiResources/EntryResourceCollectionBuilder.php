<?php

namespace CbtechLtd\Fastlane\Support\ApiResources;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class EntryResourceCollectionBuilder
{
    private Collection $items;
    private Request $request;
    private EntryType $entryType;

    public function __construct(EntryType $entryType, Collection $items, Request $request)
    {
        $this->items = $items;
        $this->request = $request;
        $this->entryType = $entryType;
    }

    public function build(): JsonResource
    {
        $resourceCollection = $this->entryType->apiResourceCollection();
        $resource = $this->entryType->apiResource();
        $fields = $this->entryType->schema()->getFields();

        /** @var EntryResourceCollection $collection */
        $collection = $resourceCollection::make(
            $this->items->map(fn($i) => (new $resource($i))->withResolvedFields($fields))->all()
        );

        $collection
            ->withLinks([
                ResourceLink::make('self', ["fastlane.api.{$this->entryType->identifier()}.collection"]),
            ])
            ->withMeta([
                ResourceMeta::make('entry_type', [
                    'singular_name' => $this->entryType->name(),
                    'plural_name'   => $this->entryType->pluralName(),
                    'identifier'    => $this->entryType->identifier(),
                ]),
            ]);

        return $collection->transform();
    }
}
