<?php

namespace CbtechLtd\Fastlane\Http\Controllers;

use CbtechLtd\Fastlane\EntryTypes\EntryInstance;
use CbtechLtd\Fastlane\Facades\EntryType;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Http\ViewModels\EntryCollectionViewModel;
use CbtechLtd\Fastlane\Http\ViewModels\EntryViewModel;
use CbtechLtd\Fastlane\Repositories\EntryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class EntryController extends Controller
{
    /**
     * Determine what entry type class this controller uses for requests.
     *
     * @var string | \CbtechLtd\Fastlane\Contracts\EntryType
     */
    protected string $entryType;

    /**
     * The instance of the repository used by the requested entry type.
     *
     * @var EntryRepository
     */
    protected EntryRepository $repository;

    /**
     * The collection of fields of the requested entry type.
     *
     * @var FieldCollection
     */
    protected FieldCollection $fields;

    /**
     * Define how many items must be shown in the listing page.
     * If no pagination is required, set it to null.
     *
     * @var int|null
     */
    protected ?int $itemsPerPage = 20;

    /**
     * EntryController constructor.
     *
     * @param EntryRepository $repository
     */
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            $this->entryType = EntryType::findByKey($request->segment(3));
            $this->repository = $this->entryType::repository();
            $this->fields = (new FieldCollection($this->entryType::fields()));

            return $next($request);
        });
    }

    /**
     * List the entries.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        // TODO: Maybe register the policy in this controller __construct method,
        //       based on what is configured in the EntryType class.
        if (Gate::getPolicyFor($this->repository->getModel())) {
            Gate::authorize('list', $this->repository->getModel());
        }

        return view()->first([
            "cp.{$this->entryType::key()}.index",
            "fastlane::{$this->entryType::key()}.index",
            'fastlane::entries.index',
        ], [
            'entryType' => $this->entryType,
            'itemsPerPage' => $this->itemsPerPage,
            'order' => [
                'field' => Str::replaceFirst('-', '', $request->input('order')),
                'isDesc' => Str::startsWith($request->input('order'), '-'),
                'str' => $request->input('order'),
            ],
            'links' => [
                'create' => Fastlane::cpRoute('entry-type.create', $this->entryType::key()),
            ],
        ]);
    }

    /**
     * Show the form to create a new entry.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create()
    {
        $entry = $this->repository->newModel();

        if (Gate::getPolicyFor($entry)) {
            Gate::authorize('create', $entry);
        }

        return view()->first([
            "cp.{$this->entryType::key()}.create",
            "fastlane::{$this->entryType::key()}.create",
            "fastlane::entries.create",
        ], [
            'entry' => EntryInstance::newFromModel($this->entryType, $this->entryType::repository()->newModel())->forCreate(),
            'links' => [
                'top' => Fastlane::cpRoute('entry-type.index', $this->entryType::key()),
            ],
        ]);
    }

    /**
     * Show the editing page for the given id.
     *
     * @param Request $request
     * @param string  $entryTypeKey
     * @param         $id
     * @return mixed
     */
    public function edit(Request $request, string $entryTypeKey, $id)
    {
        $entry = $this->repository->findOrFail($id)->forUpdate();

        if (Gate::getPolicyFor($entry->model())) {
            Gate::authorize('update', $entry->model());
        }

        return view()->first([
            "cp.{$this->entryType::key()}.edit",
            "fastlane::{$this->entryType::key()}.edit",
            "fastlane::entries.edit",
        ], [
            'entry' => $entry,
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
}
