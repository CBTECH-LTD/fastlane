<?php

namespace CbtechLtd\Fastlane\Http\Controllers\API;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Controllers\Controller;
use CbtechLtd\Fastlane\Support\ApiResources\EntryResourceBuilder;
use CbtechLtd\Fastlane\Support\ApiResources\EntryResourceCollectionBuilder;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class EntriesController extends Controller
{
    protected Fastlane $fastlane;

    public function __construct()
    {
        $this->fastlane = app('fastlane');
    }

    public function collection(Request $request)
    {
        $items = $this->entryType()->getItems();

        $collection = new EntryResourceCollectionBuilder(
            $this->entryType(),
            $items,
            $request
        );

        return response()->json($collection->build());
    }

    public function single(Request $request)
    {
        $item = new EntryResourceBuilder(
            $this->entryType(),
            $this->entry(),
            $request
        );

        return response()->json($item->build());
    }

    protected function entryType(): EntryType
    {
        return $this->fastlane->getRequestEntryType();
    }

    protected function entry(): ?Model
    {
        return $this->fastlane->getRequestEntry();
    }
}
