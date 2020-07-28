<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Requests\EntryEditRequest;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Http\Requests\EntryStoreRequest;
use CbtechLtd\Fastlane\Http\Requests\EntryUpdateRequest;
use CbtechLtd\Fastlane\Support\ApiResources\EntryResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EntriesController extends Controller
{
    public function index(EntryRequest $request)
    {
        $collection = new ApiResourceCollection(
            $request->entryType()->getItems()->map(
                fn(Model $m) => (new EntryResource($m, $request->entryType()))->toIndex()
            )
        );

        return $this->render('Entries/Index', [
            'items'     => $collection,
            'entryType' => [
                'schema'        => $request->entryType()->schema()->toIndex(),
                'singular_name' => $request->entryType()->name(),
                'plural_name'   => Str::plural($request->entryType()->name()),
            ],
            'links'     => [
                'create' => URL::relative("cp.{$request->entryType()->identifier()}.create"),
            ],
        ]);
    }

    public function create(EntryRequest $request)
    {
        // TODO: Add authorization

        return $this->render('Entries/Create', [
            'entryType' => [
                'schema'        => $request->entryType()->schema()->toCreate(),
                'singular_name' => $request->entryType()->name(),
                'plural_name'   => Str::plural($request->entryType()->name()),
            ],
            'links'     => [
                'form'   => URL::relative("cp.{$request->entryType()->identifier()}.store"),
                'parent' => URL::relative("cp.{$request->entryType()->identifier()}.index"),
            ],
        ]);
    }

    public function store(EntryStoreRequest $request)
    {
        $entry = $request
            ->entryType()
            ->store($request);

        FastlaneFacade::flashSuccess($request->entryType()->name() . ' created successfully.', 'thumbs-up');

        return Redirect::route("cp.{$request->entryType()->identifier()}.edit", [$entry]);
    }

    public function edit(EntryEditRequest $request)
    {
        $entry = $request->entry();

        return $this->render('Entries/Edit', [
            'item' => new ApiResource((new EntryResource($entry, $request->entryType()))->toUpdate()),
        ]);
    }

    public function update(EntryUpdateRequest $request, string $id)
    {
        $request->entryType()->update($request, $id);

        FastlaneFacade::flashSuccess($request->entryType()->name() . ' updated successfully.', 'thumbs-up');

        return Redirect::back();
    }

    public function delete(EntryRequest $request, string $id)
    {
        $request->entryType()->delete($id);

        FastlaneFacade::flashSuccess($request->entryType()->name() . ' deleted successfully.', 'trash');

        return Redirect::route("cp.{$request->entryType()->identifier()}.index");
    }
}
