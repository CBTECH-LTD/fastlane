<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Controllers\API;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
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
        $queryFilter = $this->fastlane->getRequest()->buildQueryFilter();

        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->entryType()->getItems($queryFilter);

        // Transform the paginator collection, which is composed of
        // entry instances, into a collection of entry resources ready
        // to be transformed to our front-end application.
        $paginator->getCollection()
            ->transform(
                fn(EntryInstance $entry) => (new EntryResource($entry))->toIndex()
            );

        $collection = EntryResourceCollection::makeFromPaginator($paginator)
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
