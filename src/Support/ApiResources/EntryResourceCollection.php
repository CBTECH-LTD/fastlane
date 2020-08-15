<?php

namespace CbtechLtd\Fastlane\Support\ApiResources;

use CbtechLtd\JsonApiTransformer\ApiResources\ResourceTypeCollection;

class EntryResourceCollection extends ResourceTypeCollection
{
    public function __construct(array $items = [])
    {
        parent::__construct($items);

        foreach ($this->items as $item) {
            $item->withOptions([
                'output' => 'collection',
            ]);
        }
    }

    public function resourceType(): string
    {
        return EntryResource::class;
    }

    protected function links(): array
    {
        return [];
    }

    protected function meta(): array
    {
        return [];
    }
}
