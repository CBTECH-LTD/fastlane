<?php

namespace CbtechLtd\Fastlane\Http\ViewModels;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\Traits\WithLinks;
use CbtechLtd\Fastlane\Http\ViewModels\Traits\WithMeta;
use CbtechLtd\Fastlane\View\Components\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\ViewModels\ViewModel;

/**
 * Class EntryCollectionViewModel
 *
 * This class provides the following properties to the view:
 *
 * - data: list of items in the collection.
 * - meta: entry type data, pagination and more metadata.
 * - links: links related to the resource.
 * - paginator: an instance of the paginator, if present.
 *
 * @package CbtechLtd\Fastlane\Http\ViewModels
 */
class EntryCollectionViewModel extends ViewModel
{
    use WithMeta, WithLinks;

    /** @var Collection|LengthAwarePaginator */
    private $data;

    private FieldCollection $fields;

    /** @var EntryType|string */
    private string $entryType;

    /** @var string[] */
    protected $ignore = ['withMeta', 'withLinks'];

    /**
     * EntryCollectionViewModel constructor.
     *
     * @param string          $entryType
     * @param FieldCollection $fields
     * @param                 $data
     */
    public function __construct(string $entryType, FieldCollection $fields, $data)
    {
        $this->data = $data;
        $this->fields = $fields;
        $this->entryType = $entryType;
    }

    /**
     * Provide the items of the collection to the view.
     *
     * @return array
     */
    public function data(): array
    {
        return $this->getItemsCollection()->all();
    }

    /**
     * Get the paginator instance if data is paginated.
     *
     * @return LengthAwarePaginator|null
     */
    public function paginator(): ?LengthAwarePaginator
    {
        return $this->hasPagination()
            ? $this->data
            : null;
    }

    /**
     * Check whether the collection is a paginator instance.
     *
     * @return bool
     */
    private function hasPagination(): bool
    {
        return $this->data instanceof LengthAwarePaginator;
    }

    /**
     * Build the links Data Accessor.
     *
     * @return Collection
     */
    protected function getDefaultLinks(): Collection
    {
        return Collection::make([])
            ->when($this->entryType::routes()->has('index'), function (Collection $c) {
                return $c->put('self', $this->entryType::routes()->get('index')->url());
            })
            ->when($this->entryType::routes()->has('create'), function (Collection $c) {
                return $c->put('create', $this->entryType::routes()->get('create')->url());
            });
    }

    /**
     * Build the meta data accessor.
     *
     * @return Collection
     */
    protected function getDefaultMeta(): Collection
    {
        return Collection::make(
            (! blank($paginatorMeta = $this->buildPaginatorMeta()))
                ? ['paginator' => $paginatorMeta]
                : []
        );
    }

    /**
     * @inheritDoc
     */
    protected function buildSchemaForMeta()
    {
        return $this->fields->flattenFields();
    }

    /**
     * Build the meta array containing paginator information.
     *
     * @return array
     */
    private function buildPaginatorMeta(): array
    {
        if (! $this->hasPagination()) {
            return [];
        }

        return (new Paginator($this->data))->toArray();
    }

    /**
     * @return Collection
     */
    private function getItemsCollection(): Collection
    {
        return Collection::make(
            ($this->hasPagination()) ? $this->data->items() : $this->data->all()
        );
    }
}
