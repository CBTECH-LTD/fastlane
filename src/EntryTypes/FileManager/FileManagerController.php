<?php

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\Contracts\QueryBuilder;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Controllers\EntriesController;
use CbtechLtd\Fastlane\Http\Requests\CreateRequest;
use CbtechLtd\Fastlane\Http\Requests\IndexRequest;
use CbtechLtd\Fastlane\Http\Transformers\EntryTypeResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class FileManagerController extends EntriesController
{
    public function index(IndexRequest $request)
    {
        /** @var Collection $items */
        $items = $request->entryType()::queryListing(false, function (QueryBuilder $builder) use ($request) {
            return $request->buildQueryFilter()
                ->addOrder('created_at:desc')
                ->pipeThrough($builder);
        });

        $collection = EntryTypeResourceCollection::toListing(
            $request->entryType(),
            $items->all()
        )->withMeta([
            'order' => $request->input('order'),
        ])->withLinks([
            'upload' => $request->entryType()::routes()->get('store')->url(),
        ]);

        // If the request wants a JSON response, we just return the
        // collection array, otherwise we return the complete view.
        if ($request->expectsJson()) {
            return $collection->toArray();
        }

        return $this->render('FileManager/Index', [
            'items' => $collection->toArray(),
        ]);
    }


    public function create(CreateRequest $request)
    {
        return redirect()->route("cp.{$this->entryType()->identifier()}.index");
    }

    public function store(CreateRequest $request)
    {
        $request->entryType()->store($request->all());

        if ($request->expectsJson()) {
            return response()->json([], Response::HTTP_CREATED);
        }
        
        Fastlane::flashSuccess(__('fastlane::core.flash.created', ['name' => $request->entryType()::name()]), 'thumbs-up');

        return redirect($request->entryType()::routes()->get('index')->url());
    }
}
