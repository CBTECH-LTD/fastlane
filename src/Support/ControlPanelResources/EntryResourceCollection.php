<?php

namespace CbtechLtd\Fastlane\Support\ControlPanelResources;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Http\Transformers\EntryResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceTypeCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EntryResourceCollection extends ResourceTypeCollection
{
    protected EntryType $entryType;

    public static function makeFromPaginator(LengthAwarePaginator $paginator): self
    {
        $instance = new static($paginator->items());

        $pages = Collection::make($paginator->getUrlRange($paginator->currentPage() - 3, $paginator->currentPage() + 3))
            ->filter(fn($v, $k) => $k > 0 && $k <= $paginator->lastPage())
            ->map(fn($v, $k) => [
                'number'    => $k,
                'url'       => $v,
                'isCurrent' => $k === $paginator->currentPage(),
            ])
            ->values();

        $instance->withMeta([
            ResourceMeta::make('firstPageUrl', $paginator->path()),
            ResourceMeta::make('previousPageUrl', $paginator->previousPageUrl()),
            ResourceMeta::make('pageUrls', $pages),
            ResourceMeta::make('nextPageUrl', $paginator->nextPageUrl()),
            ResourceMeta::make('lastPageUrl', $paginator->url($paginator->lastPage())),
            ResourceMeta::make('currentPage', $paginator->currentPage()),
            ResourceMeta::make('lastPage', $paginator->lastPage()),
            ResourceMeta::make('total', $paginator->total()),
        ]);

        return $instance;
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
            ResourceLink::make('create', [$this->entryType::routes()->get('create')->routeName()]),
        ];
    }

    protected function meta(): array
    {
        return [
            ResourceMeta::make('entry_type', [
                'schema'        => $this->entryType->getFields()->onListing()->flattenFields()->toArray(),
                'singular_name' => $this->entryType::name(),
                'plural_name'   => $this->entryType::pluralName(),
                'identifier'    => $this->entryType::key(),
                'icon'          => $this->entryType::icon(),
            ]),
        ];
    }
}
