<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryCollectionViewModel;
use CbtechLtd\Fastlane\Models\Entry;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ListingTable extends ReactiveComponent
{
    public bool $dataLoaded = false;
    public string $entryType;
    public int $itemsPerPage;
    public int $page = 1;
    public ?string $order = null;

    /** @var string[] */
    protected $queryString = ['page', 'order'];

    public function mount(string $entryType, int $itemsPerPage, ?string $order = null, int $page = 1)
    {
        $this->entryType = $entryType;
        $this->itemsPerPage = $itemsPerPage;
        $this->page = $page;
        $this->order = $order ?? $entryType::listingDefaultOrder();
    }

    public function loadData(): void
    {
        $this->dataLoaded = true;
    }

    public function changeOrder(string $order): void
    {
        $this->page = 1;
        $this->order = $order;
    }

    public function render()
    {
        $data = $this->buildItems();

        return view('fastlane::components.livewire.listing-table', [
            'items'     => $data['items'],
            'paginator' => $data['paginator'],
            'fields'    => $this->getFields()->getAttributes(),
            'meta'      => $this->buildMeta(),
            'links'     => $this->buildLinks(),
        ]);
    }

    /**
     * Get fields of the entry type.
     *
     * @return FieldCollection
     */
    protected function getFields(): FieldCollection
    {
        return once(fn() => (new FieldCollection($this->entryType::fields()))->onListing());
    }

    protected function buildItems(): array
    {
        $columns = $this->getFields()->getCollection()->map(fn (Field $field) => $field->getAttribute())->all();

        $items = $this->dataLoaded
            ? $this->entryType::repository()->get($columns, $this->buildFilters(), $this->itemsPerPage)
            : new Collection();

        return [
            'items'     => $items->all(),
            'paginator' => $items instanceof LengthAwarePaginator ? $items : null,
        ];
    }

    protected function buildMeta(): array
    {
        return [
            'order' => [
                'field' => Str::replaceFirst('-', '', $this->order),
                'dir'   => Str::startsWith($this->order, '-') ? 'desc' : 'asc',
            ],
        ];
    }

    protected function buildLinks(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected function buildFilters(): array
    {
        $filters = [];

        if ($this->order) {
            $filters['order'] = Str::startsWith($this->order, '-')
                ? (Str::replaceFirst('-', '', $this->order) . ':desc')
                : $this->order . ':asc';
        }

        return $filters;
    }
}
