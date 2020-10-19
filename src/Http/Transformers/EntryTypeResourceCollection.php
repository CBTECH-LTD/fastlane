<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Transformers;

use CbtechLtd\Fastlane\Contracts\EntryType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class EntryTypeResourceCollection implements Arrayable
{
    protected EntryType $entryType;
    protected array $items;
    protected ?LengthAwarePaginator $paginator;
    protected array $meta = [];
    protected array $links = [];

    public static function make(EntryType $entryType, array $items): self
    {
        return new static($entryType, $items);
    }

    public function __construct(EntryType $entryType, array $items)
    {
        $this->entryType = $entryType;
        $this->items = $items;
    }

    public function withMeta(array $data): self
    {
        $this->meta = array_merge($this->meta, $data);
        return $this;
    }

    public function withLinks(array $links): self
    {
        $this->links = array_merge($this->links, $links);
        return $this;
    }

    public function withPaginator(LengthAwarePaginator $paginator): self
    {
        $this->paginator = $paginator;
        return $this;
    }

    public function toArray()
    {
        return [
            'type'  => $this->entryType::key(),
            'data'  => $this->buildData(),
            'meta'  => $this->buildMeta(),
            'links' => $this->buildLinks(),
        ];
    }

    protected function buildMeta(): array
    {
        $meta = [
            'entry_type' => [
                'identifier'    => $this->entryType::key(),
                'singular_name' => $this->entryType::name(),
                'plural_name'   => $this->entryType::pluralName(),
                'icon'          => $this->entryType::icon(),
                'schema'        => $this->entryType->getFields()->onListing()->flattenFields()->toArray(),
            ],
        ];

        return array_merge($meta, $this->buildPaginatorMeta(), $this->meta);
    }

    /**
     * @return array
     */
    protected function buildData(): array
    {
        return Collection::make($this->items)->map(function (EntryType $entryType) {
            return $entryType->toResource()->toListing();
        })->filter()->toArray();
    }

    protected function buildPaginatorMeta(): array
    {
        if (! isset($this->paginator)) {
            return [];
        }

        $pages = Collection::make($this->paginator->getUrlRange($this->paginator->currentPage() - 3, $this->paginator->currentPage() + 3))
            ->filter(fn($v, $k) => $k > 0 && $k <= $this->paginator->lastPage())
            ->map(fn($v, $k) => [
                'number'    => $k,
                'url'       => $v,
                'isCurrent' => $k === $this->paginator->currentPage(),
            ])
            ->values();

        return [
            'firstPageUrl'    => $this->paginator->path(),
            'previousPageUrl' => $this->paginator->previousPageUrl(),
            'pageUrls'        => $pages,
            'nextPageUrl'     => $this->paginator->nextPageUrl(),
            'lastPageUrl'     => $this->paginator->url($this->paginator->lastPage()),
            'currentPage'     => $this->paginator->currentPage(),
            'lastPage'        => $this->paginator->lastPage(),
            'total'           => $this->paginator->total(),
        ];
    }

    protected function buildLinks(): array
    {
        return Collection::make()

            // Add some default links...
            ->when($this->entryType::routes()->has('index'), function (Collection $c) {
                return $c->put('self', $this->entryType::routes()->get('index')->url());
            })
            ->when($this->entryType::routes()->has('create'), function (Collection $c) {
                return $c->put('create', $this->entryType::routes()->get('create')->url());
            })

            // Merge with links provided via withLinks method and transform to array.
            ->merge($this->links)
            ->map(function ($value) {
                return is_callable($value)
                    ? call_user_func($value, $this->entryType)
                    : $value;
            })->toArray();
    }
}
