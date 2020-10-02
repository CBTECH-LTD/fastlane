<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Controllers\API;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
use CbtechLtd\Fastlane\QueryFilter\Pipes\WhereIn;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResource;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResourceCollection;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class FileManagerController extends Controller
{
    protected Fastlane $fastlane;

    public function __construct()
    {
        $this->fastlane = app('fastlane');
    }

    public function index(Request $request)
    {
        $queryFilter = $this->fastlane->getRequest()->buildQueryFilter()
            ->addOrder('name', 'asc')
            ->addOrder('created_at', 'desc');

        if ($types = $request->input('filter.types')) {
            $queryFilter->addFilter(new WhereIn('mimetype', $types));
        }

        $items = $queryFilter
            ->pipeThrough($this->entryType()->queryBuilder())
            ->get()
            ->map(
                fn(EntryInstance $entry) => (new EntryResource($entry))->toIndex()
            );

        /** @var LengthAwarePaginator $paginator */
//        $paginator = $this->entryType()->getItems($queryFilter);

        $collection = EntryResourceCollection::make($items->all())
            ->forEntryType($this->entryType())
            ->withMeta([
                ResourceMeta::make('order', $this->fastlane->getRequest()->input('order')),
            ]);

        return $collection->transform();
    }

    public function store(Request $request)
    {
        $this->entryType()->store($request);

        return response()->json([], Response::HTTP_CREATED);
    }

    protected function entryType(): EntryType
    {
        return $this->fastlane->getRequest()->getEntryType();
    }
}
