<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResource;
use CbtechLtd\Fastlane\Support\ControlPanelResources\EntryResourceCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
        return $this->fastlane->getRequestEntryType();
    }

    protected function entry(): ?Model
    {
        return $this->fastlane->getRequestEntry();
    }

    public function index()
    {
        $collection = EntryResourceCollection::make(
            $this->entryType()->getItems()->map(
                fn(Model $m) => (new EntryResource($m, $this->entryType()))->toIndex()
            )->all()
        )->forEntryType($this->entryType());

        return $this->render('Entries/Index', [
            'items'     => $collection->transform(),
            'entryType' => [
                'schema'        => $this->entryType()->schema()->getIndexFields(),
                'singular_name' => $this->entryType()->name(),
                'plural_name'   => Str::plural($this->entryType()->name()),
            ],
            'links'     => [
                'create' => URL::relative("cp.{$this->entryType()->identifier()}.create"),
            ],
        ]);
    }

    public function create()
    {
        if ($this->entryType()->policy()) {
            $this->authorize('create', $this->entryType()->model());
        }

        return $this->render('Entries/Create', [
            'entryType' => [
                'schema'        => $this->entryType()->schema()->getCreateFields(),
                'panels'        => Collection::make($this->entryType()->schema()->getPanels()),
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

        return Redirect::route("cp.{$this->entryType()->identifier()}.edit", [$entry]);
    }

    public function edit()
    {
        return $this->render('Entries/Edit', [
            'item' => (new EntryResource($this->entry(), $this->entryType()))->toUpdate()->transform(),
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
