<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Contracts\QueryBuilder;
use CbtechLtd\Fastlane\Contracts\WithCustomViews;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Requests\CreateRequest;
use CbtechLtd\Fastlane\Http\Requests\DeleteRequest;
use CbtechLtd\Fastlane\Http\Requests\IndexRequest;
use CbtechLtd\Fastlane\Http\Requests\UpdateRequest;
use CbtechLtd\Fastlane\Http\Transformers\EntryTypeResourceCollection;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionLinks;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionMeta;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class EntriesController extends Controller
{
    /**
     * @param IndexRequest $request
     * @return RedirectResponse|Response
     */
    public function index(IndexRequest $request)
    {
        $paginator = $request->entryType()::queryListing(true, function (QueryBuilder $builder) use ($request) {
            return $request->buildQueryFilter()
                ->addOrder('created_at')
                ->addOrder('id')
                ->pipeThrough($builder);
        });

        // Redirect to the first pagination page if the requested page
        // is bigger than the maximum existent page in the paginator.
        if ($paginator->currentPage() > $paginator->lastPage()) {
            return redirect()->route(
                $request->entryType()::routes()->get('index')->routeName(),
                Collection::make(request()->query())->except(['page'])->all()
            );
        }

        // Transform the paginator collection, which is composed of
        // entry instances, into a collection of entry resources ready
        // to be transformed to our front-end application.
        $collection = EntryTypeResourceCollection::make(
            $request->entryType(),
            $paginator->items()
        )->withPaginator($paginator)->withMeta([
            'order' => $request->input('order'),
        ]);

        // Check whether the entry type implements custom collection links
        // and collection meta then add them to the entry collection.
        if ($request->entryType() instanceof WithCollectionLinks) {
            $collection->withLinks($request->entryType()->collectionLinks());
        }

        if ($request->entryType() instanceof WithCollectionMeta) {
            $collection->withMeta($request->entryType()->collectionMeta());
        }

        // Return the collection to the expected view. The entry type
        // may define its custom view component, otherwise we just
        // return the default index view.
        $view = function () use ($request) {
            return $request->entryType() instanceof WithCustomViews
                ? $request->entryType()->getListingView()
                : null;
        };

        return $this->render($view() ?? 'Entries/Index', [
            'items' => $collection->toArray(),
        ]);
    }

    /**
     * @param CreateRequest $request
     * @return Response
     */
    public function create(CreateRequest $request)
    {
        $view = function () use ($request) {
            return $request->entryType() instanceof WithCustomViews
                ? $request->entryType()->getCreateView()
                : null;
        };

        return $this->render($view() ?? 'Entries/Create', [
            'item' => $request->entryType()->toResource()->toCreate()->withSchema()->toArray(),
        ]);
    }

    /**
     * @param CreateRequest $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreateRequest $request)
    {
        $entry = $request->entryType()->store($request->all());

        Fastlane::flashSuccess(
            __('fastlane::core.flash.created', ['name' => $request->entryType()::name()]),
            'thumbs-up'
        );

        return redirect(
            $request->entryType()::routes()
                ->get('edit')
                ->url([$entry->entryRouteKey()])
        );
    }

    /**
     * @param UpdateRequest $request
     * @return Response
     */
    public function edit(UpdateRequest $request)
    {
        $view = function () use ($request) {
            return $request->entryType() instanceof WithCustomViews
                ? $request->entryType()->getEditView()
                : null;
        };

        return $this->render($view() ?? 'Entries/Edit', [
            'item' => $request->entryType()->toResource()->toUpdate()->toArray(),
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request)
    {
        $request->entryType()->update($request->all());

        Fastlane::flashSuccess(
            __('fastlane::core.flash.updated', ['name' => $request->entryType()::name()]),
            'thumbs-up'
        );

        return Redirect::back();
    }

    /**
     * @param DeleteRequest $request
     * @return Application|RedirectResponse|Redirector
     * @throws Exception
     */
    public function delete(DeleteRequest $request)
    {
        $request->entryType()->delete();

        Fastlane::flashSuccess(
            __('fastlane::core.flash.deleted', ['name' => $request->entryType()::name()]),
            'trash'
        );

        return redirect($request->entryType()::routes()->get('index')->url());
    }
}
