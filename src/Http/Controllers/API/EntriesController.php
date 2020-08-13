<?php

namespace CbtechLtd\Fastlane\Http\Controllers\API;

use CbtechLtd\Fastlane\Http\Controllers\Controller;
use CbtechLtd\Fastlane\Http\Requests\API\EntryRequest;
use Illuminate\Database\Eloquent\Builder;

class EntriesController extends Controller
{
    public function collection(EntryRequest $request)
    {
        $items = $request->entryType()->getItems(function (Builder $query) {
            $query->where('is_active', true);
        });

        $resource = $request->entryType()->apiResource();
        $resourceCollection = $request->entryType()->apiResourceCollection();

        $collection = $resourceCollection::make(
            $items->map(fn($item) => new $resource($item, $request))->all()
        )->forEntryType($request->entryType());

        return response()->json($collection->transform());
    }
}
