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

        if (Gate::getPolicyFor($repository->getModel())) {
            Gate::authorize('list', $repository->getModel());
        }

        $fields = $this->getFields()->onListing();

        $viewModel = (new EntryCollectionViewModel($this->entryType, $fields, []))
            ->withMeta([
                'itemsPerPage' => $this->itemsPerPage,
                'order'        => [
                    'field'  => Str::replaceFirst('-', '', $request->input('order')),
                    'isDesc' => Str::startsWith($request->input('order'), '-'),
                ],
                'order_str'    => $request->input('order'),
            ]);

        return view()->first([
            "cp.{$this->entryType::key()}.index",
            "fastlane::{$this->entryType::key()}.index",
            'fastlane::entries.index',
        ], $viewModel);
    }

    /**
     * Show the form to create a new entry.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create()
    {
        $entry = $this->getRepository()->newModel();

        if (Gate::getPolicyFor($entry)) {
            Gate::authorize('create', $entry);
        }

        $fields = $this->getFields()->onCreate();
        $viewModel = new EntryViewModel($this->entryType, $fields, $entry);

        return view()->first([
            "cp.{$this->entryType::key()}.create",
            "fastlane::{$this->entryType::key()}.create",
            "fastlane::entries.create",
        ], $viewModel);
    }

    /**
     * Store the new entry.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $model = $this->getRepository()->getModel();

        if (Gate::getPolicyFor($model)) {
            Gate::authorize('create', $model);
        }

        $fields = $this->getFields()->onCreate();
        $entry = $this->getRepository()->store($request->all());

        Fastlane::flashSuccess(
            __('fastlane::core.flash.created', ['name' => $this->entryType::label()['singular']]),
            'thumbs-up'
        );

        if ($this->entryType::routes()->has('edit')) {
            return Redirect::to($this->entryType::routes()->get('edit')->url($entry));
        }

        if ($this->entryType::routes()->get('index')) {
            return Redirect::to($this->entryType::routes()->get('index')->url());
        }

        return Redirect::back();
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

        if (Gate::getPolicyFor($entry)) {
            Gate::authorize('update', $entry);
        }

        return view()->first([
            "cp.{$this->entryType::key()}.edit",
            "fastlane::{$this->entryType::key()}.edit",
            "fastlane::entries.edit",
        ], [
            'model' => $entry,
            'entryType' => $this->entryType,
        ]);
    }

    /**
     * Update the given entry.
     *
     * @param Request $request
     * @param         $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update(Request $request, $id)
    {
        $entry = $this->getRepository()->findOrFail($id);

        if (Gate::getPolicyFor($entry)) {
            Gate::authorize('update', $entry);
        }

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
