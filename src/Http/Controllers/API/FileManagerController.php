<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Controllers\API;

use CbtechLtd\Fastlane\EntryTypes\FileManager\WhereMimetype;
use CbtechLtd\Fastlane\EntryTypes\FileManager\WhereParent;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResource;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResourceCollection;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
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
        $parent = (!is_null($request->input('filter.parent')))
            ? $this->entryType()->queryBuilder()->key($request->input('filter.parent'))->firstOrFail()->model()
            : null;

        $queryFilter = $this->fastlane->getRequest()->buildQueryFilter()
            ->addOrder('-created_at')
            ->addOrder('name')
            ->addFilter(new WhereParent($request->input('filter.parent')));

        if ($types = $request->input('filter.types')) {
            $queryFilter->addFilter(new WhereMimetype($types));
        }

        $items = $queryFilter
            ->pipeThrough($this->entryType()->queryBuilder())
            ->get()
            ->mapToGroups(function (EntryInstance $e) {
                $type = $e->model()->isDirectory() ? 'directories' : 'files';

                return [ $type => $e ];
            })
            ->sortKeys()
            ->flatMap
            ->map(
                fn(EntryInstance $entry) => (new EntryResource($entry))->toIndex()
            );

        $collection = EntryResourceCollection::make($items->all())
            ->forEntryType($this->entryType())
            ->withLinks([
                ResourceLink::make('top', ['cp.file-manager.index', ['parent' => optional($parent)->parent_id]])->when(!is_null($parent)),
            ])
            ->withMeta([
                ResourceMeta::make('order', $this->fastlane->getRequest()->input('order')),
                ResourceMeta::make('directory', optional($parent)->toArray())->when(!is_null($parent)),
            ]);

        return $collection->transform();
    }

    public function store(Request $request)
    {
        $this->entryType()->store($request);

        return response()->json([], Response::HTTP_CREATED);
    }

    public function moveToDirectory(Request $request)
    {
        $data = $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required',
            'target' => 'nullable|string',
        ]);

        $this->entryType()->moveFiles($data['files'], $data['target'] ?? null);
    }

    protected function entryType(): EntryType
    {
        return $this->fastlane->getRequest()->getEntryType();
    }
}
