<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Http\Requests\EntryStoreRequest;
use CbtechLtd\Fastlane\Http\Requests\EntryUpdateRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class EntriesController extends Controller
{
    public function index(EntryRequest $request)
    {
        $items = $request->entryType()->getItems();

        return $this->render('Entries/Index', [
            'items'     => $items,
            'entryType' => [
                'schema'        => $request->entryType()->schema()->getDefinition()->toIndex()->toArray(),
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
                'schema'        => $request->entryType()->schema()->getDefinition()->toCreate()->toArray(),
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

        return Redirect::route("cp.{$request->entryType()->identifier()}.edit", [$entry]);
    }

    public function edit(EntryRequest $request, string $id)
    {
        $entry = $request->entryType()->findItem($id);

        return $this->render('Entries/Edit', [
            'item'      => $entry,
            'entryType' => [
                'schema'        => $request->entryType()->schema()->getDefinition()->toUpdate()->toArray(),
                'singular_name' => $request->entryType()->name(),
                'plural_name'   => Str::plural($request->entryType()->name()),
            ],
        ]);
    }

    public function update(EntryUpdateRequest $request, string $id)
    {
        $request->entryType()->update($request, $id);

        session()->flash('message', [
            'type' => 'success',
            'text' => 'User updated successfully',
        ]);

        return Redirect::back();
    }

    public function delete(EntryRequest $request, string $id)
    {
        $request->entryType()->delete($id);

        return Redirect::route('cp.backend-users.index');
    }
}
