<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionLinks;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionMeta;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomViews;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResource;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResourceCollection;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceMeta;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EntriesController extends Controller
{
    protected Fastlane $fastlane;

    public function __construct()
    {
        $this->fastlane = app('fastlane');
    }

    protected function entryType(): EntryType
    {
        return $this->fastlane->getRequest()->getEntryType();
    }

    protected function entryInstance(): ?EntryInstance
    {
        return $this->fastlane->getRequest()->getEntryInstance();
    }

    public function index()
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $this->entryType()->getItems(
            $this->fastlane->getRequest()->buildQueryFilter()
        );

        // Redirect to the first pagination page if the requested page
        // is bigger than the maximum existent page in the paginator.
        if ($paginator->currentPage() > $paginator->lastPage()) {
            return redirect()->route(
                "cp.{$this->entryType()->identifier()}.index",
                Collection::make(request()->query())->except(['page'])->all()
            );
        }

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

        if ($this->entryType() instanceof WithCollectionLinks) {
            $collection->withLinks($this->entryType()->collectionLinks());
        }

        if ($this->entryType() instanceof WithCollectionMeta) {
            $collection->withMeta($this->entryType()->collectionMeta());
        }

        // Return the transformed collection and some important information like
        // entry type schema, names and links.
        $view = function () {
            return $this->entryType() instanceof WithCustomViews
                ? $this->entryType()->getIndexView()
                : null;
        };

        return $this->render($view() ?? 'Entries/Index', [
            'items' => $collection->transform(),
        ]);
    }

    public function create()
    {
        if ($this->entryType()->policy()) {
            $this->authorize('create', $this->entryType()->model());
        }

        $view = function () {
            return $this->entryType() instanceof WithCustomViews
                ? $this->entryType()->getCreateView()
                : null;
        };

        return $this->render($view() ?? 'Entries/Create', [
            'item'      => (new EntryResource($this->entryInstance()))->toCreate()->transform(),
            'entryType' => [
                'schema'        => $this->entryInstance()->schema()->getCreateFields(),
                'panels'        => Collection::make($this->entryInstance()->schema()->getPanels()),
                'singular_name' => $this->entryType()->name(),
                'plural_name'   => Str::plural($this->entryType()->name()),
            ],
            'links'     => [
                'form'   => URL::relative("cp.{$this->entryType()->identifier()}.store"),
                'parent' => URL::relative("cp.{$this->entryType()->identifier()}.index"),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $entry = $this->entryType()->store($request);

        FastlaneFacade::flashSuccess($this->entryType()->name() . ' created successfully.', 'thumbs-up');

        return Redirect::route("cp.{$this->entryType()->identifier()}.edit", [$entry->model()]);
    }

    public function edit()
    {
        if ($this->entryType()->policy()) {
            $this->authorize('update', $this->entryInstance()->model());
        }

        $view = function () {
            return $this->entryType() instanceof WithCustomViews
                ? $this->entryType()->getEditView()
                : null;
        };

        return $this->render($view() ?? 'Entries/Edit', [
            'item' => (new EntryResource($this->entryInstance()))->toUpdate()->transform(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $this->entryType()->update($request, $id);

        $this->fastlane->flashSuccess(
            $this->entryType()->name() . ' updated successfully.',
            'thumbs-up'
        );

        return Redirect::back();
    }

    public function delete(Request $request, string $id)
    {
        $this->entryType()->delete($id);

        FastlaneFacade::flashSuccess($this->entryType()->name() . ' deleted successfully.', 'trash');

        return Redirect::route("cp.{$this->entryType()->identifier()}.index");
    }
}
