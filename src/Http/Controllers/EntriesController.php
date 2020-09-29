<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Contracts\QueryBuilder;
use CbtechLtd\Fastlane\Contracts\WithCustomViews;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Requests\CreateRequest;
use CbtechLtd\Fastlane\Http\Requests\DeleteRequest;
use CbtechLtd\Fastlane\Http\Requests\IndexRequest;
use CbtechLtd\Fastlane\Http\Requests\UpdateRequest;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResource;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Response;

class EntriesController extends Controller
{
    /**
     * @param IndexRequest $request
     * @return Application|\Illuminate\Contracts\View\Factory|RedirectResponse|\Illuminate\View\View|Response
     */
    public function index(IndexRequest $request)
    {
        $paginator = $request->entryType()::queryListing(function (QueryBuilder $builder) use ($request) {
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

        // Use a custom view if the entry type defines one, otherwise
        // just use the default entries view.
        $view = function () use ($request) {
            return $request->entryType() instanceof WithCustomViews
                ? $request->entryType()->getListingView()
                : null;
        };

        $columns = $request->entryType()
            ->getFields()
            ->onListing()
            ->flattenFields()
            ->all();

        return view($view() ?? 'fastlane::entries.index', [
            'items'          => $paginator->items(),
            'columns'        => $columns,
            'paginator'      => $paginator,
            'type'           => $request->entryType(),
            'orderBy'        => $request->input('order'),
            'orderDirection' => Str::startsWith($request->input('order'), '-') ? 'desc' : 'asc',
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
            'item'      => (new EntryResource($request->entryType()))->toCreate()->transform(),
            'entryType' => [
                'schema'        => $request->entryType()->getFields()->onCreate(),
                'panels'        => $request->entryType()->getFields()->panels(),
                'singular_name' => $request->entryType()::name(),
                'plural_name'   => $request->entryType()::pluralName(),
            ],
            'links'     => [
                'form'   => $request->entryType()::routes()->get('store')->url(null, false),
                'parent' => $request->entryType()::routes()->get('index')->url(null, false),
            ],
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
                ->url([$entry->modelInstance()])
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
            'item' => (new EntryResource($request->entryType()))->toUpdate()->transform(),
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
