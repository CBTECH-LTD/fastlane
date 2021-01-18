<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\View\Components\Listing;

use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryCollectionViewModel;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use Illuminate\Support\Str;

class ListingTable extends ReactiveComponent
{
    public bool $dataLoaded = true;
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
        $this->order = $order;
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
        $viewModel = $this->buildViewModel();

        return view('fastlane::components.livewire.listing-table', [
            'items'     => $viewModel->data(),
            'meta'      => $viewModel->meta(),
            'links'     => $viewModel->links(),
            'paginator' => $viewModel->paginator(),
        ]);
    }

    /**
     * Get fields of the entry type.
     *
     * @return FieldCollection
     */
    protected function getFields(): FieldCollection
    {
        return once(fn() => new FieldCollection($this->entryType::fields()));
    }

    protected function buildViewModel(): EntryCollectionViewModel
    {
        $fields = $this->getFields()->onListing();
        $columns = $fields->getAttributes()->keys()->all();

        $data = $this->dataLoaded
            ? $this->entryType::repository()->get($columns, $this->buildFilters(), $this->itemsPerPage)
            : [];

        return (new EntryCollectionViewModel($this->entryType, $fields, $data))
            ->withMeta([
                'order' => [
                    'field' => Str::replaceFirst('-', '', $this->order),
                    'dir'   => Str::startsWith($this->order, '-') ? 'desc' : 'asc',
                ],
            ]);
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
