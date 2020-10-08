<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Controllers\API;

use CbtechLtd\Fastlane\EntryTypes\FileManager\WhereMimetype;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResource;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResourceCollection;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
            ->addOrder('name')
            ->addOrder('-created_at');

        if ($types = $request->input('filter.types')) {
            $queryFilter->addFilter(new WhereMimetype($types));
        }

        $items = $queryFilter
            ->pipeThrough($this->entryType()->queryBuilder())
            ->get()
            ->map(
                fn(EntryInstance $entry) => (new EntryResource($entry))->toIndex()
            );
        
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
