<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryCollectionViewModel;
use CbtechLtd\Fastlane\Http\ViewModels\EntryViewModel;
use CbtechLtd\Fastlane\Repositories\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class EntryController extends Controller
{
    /**
     * Determine what entry type class this controller uses for requests.
     *
     * @var string
     */
    protected string $entryType;

    /**
     * Define how many items must be shown in the listing page.
     * If no pagination is required, set it to null.
     *
     * @var int|null
     */
    protected ?int $itemsPerPage = 20;

    /**
     * List the entries.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $repository = $this->getRepository();

        Gate::authorize('list', $repository->getModel());

        $fields = $this->getFields()->onListing();
        $columns = $fields->getAttributes()->keys()->all();

        $data = $repository->get($columns, $this->itemsPerPage);

        $viewModel = (new EntryCollectionViewModel($this->entryType, $fields, $data))
            ->withMeta([
                'order' => [
                    'field'  => Str::replaceFirst('-', '', $request->input('order')),
                    'isDesc' => Str::startsWith($request->input('order'), '-'),
                ],
            ]);

        return view()->first([
            "cp.{$this->entryType::key()}.index",
            "fastlane::{$this->entryType::key()}.index",
            'fastlane::entries.index',
        ], $viewModel);
    }

    public function create()
    {
        $entry = $this->getRepository()->newModel();

        Gate::authorize('create', $entry);

        $fields = $this->getFields()->onCreate();
        $viewModel = new EntryViewModel($this->entryType, $fields, $entry);

        return view()->first([
            "cp.{$this->entryType::key()}.create",
            "fastlane::{$this->entryType::key()}.create",
            "fastlane::entries.create",
        ], $viewModel);
    }

    /**
     * Show the editing page for the given id.
     *
     * @param Request $request
     * @param         $id
     * @return mixed
     */
    public function edit(Request $request, $id)
    {
        $entry = $this->getRepository()->findOrFail($id);

        Gate::authorize('update', $entry);

        $fields = $this->getFields()->onUpdate();
        $viewModel = new EntryViewModel($this->entryType, $fields, $entry);

        return view()->first([
            "cp.{$this->entryType::key()}.edit",
            "fastlane::{$this->entryType::key()}.edit",
            "fastlane::entries.edit",
        ], $viewModel);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('update', $this->getRepository()->findOrFail($id));

        $this->getRepository()->update($id, $request->all());

        Fastlane::flashSuccess(
            __('fastlane::core.flash.updated', ['name' => $this->entryType::label()['singular']]),
            'thumbs-up'
        );

        return Redirect::back();
    }

    /**
     * Get fields of the entry type.
     *
     * @return FieldCollection
     */
    protected function getFields(): FieldCollection
    {
        return once(fn() => new FieldCollection($this->entryType::fields()));
    }

    /**
     * Get the repository of the entry type.
     *
     * @return Repository
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function getRepository(): Repository
    {
        return $this->entryType::repository();
    }
}
