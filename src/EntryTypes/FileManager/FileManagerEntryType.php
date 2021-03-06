<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\FileAttachment\Contracts\PersistentAttachmentHandler;
use CbtechLtd\Fastlane\Support\Concerns\RendersOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionLinks;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomController;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomViews;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithRules;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldValue;
use CbtechLtd\Fastlane\Support\Schema\Fields\HiddenField;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceLink;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FileManagerEntryType extends EntryType implements WithCustomViews, WithCollectionLinks, WithCustomController, RenderableOnMenu
{
    use RendersOnMenu;

    const PERM_MANAGE_FILES = 'manage files';

    public function identifier(): string
    {
        return __('fastlane::core.file_manager.identifier');
    }

    public function model(): string
    {
        return File::class;
    }

    public function name(): string
    {
        return __('fastlane::core.file_manager.singular_name');
    }

    public function pluralName(): string
    {
        return __('fastlane::core.file_manager.plural_name');
    }

    protected function menuGroup(): string
    {
        return __('fastlane::core.menu.system_group');
    }

    public function icon(): string
    {
        return 'image';
    }

    public function fields(): array
    {
        return [
            StringField::make('name', 'Name')->required()->showOnIndex(),
            StringField::make('file', 'File')->required()->showOnIndex(),
            StringField::make('extension', 'Extension')->maxLength(10)->hideOnForm()->showOnIndex(),
            ToggleField::make('is_active', 'Active')->required()->hideOnForm()->showOnIndex(),
            HiddenField::make('size', 'Size')->hideOnForm()->showOnIndex(),
            HiddenField::make('mimetype', 'Mime Type')->hideOnForm()->showOnIndex(),
            HiddenField::make('parent_id', 'Parent Directory')->showOnIndex()->withRules([
                Rule::exists('fastlane_files', 'id')->where('mimetype', 'fastlane/directory'),
            ]),
            HiddenField::make('url', 'URL')
                ->hideOnForm()
                ->showOnIndex()
                ->readValueUsing(function (EntryInstanceContract $entryInstance) {
                    return new FieldValue('url', $entryInstance->model()->url());
                }),
        ];
    }

    public function getIndexView(): ?string
    {
        return 'FileManager/Index';
    }

    public function getCreateView(): ?string
    {
        return null;
    }

    public function getEditView(): ?string
    {
        return null;
    }

    public function collectionLinks(): array
    {
        return [
            ResourceLink::make('fileManager', ["cp.file-manager.index"]),
            ResourceLink::make('upload', ["cp.{$this->identifier()}.store"]),
        ];
    }

    public function getController(): string
    {
        return FileManagerController::class;
    }

    public function store(Request $request): EntryInstanceContract
    {
        // Check whether the authenticated user can create an
        // instance of the given entry type.
        if ($this->policy()) {
            $this->gate->authorize('create', $this->model());
        }

        $entryInstance = $this->newInstance(null);

        // If the request is trying to create a directory...
        if ($request->has('directory')) {
            return $this->makeDirectory($entryInstance, $request->input('directory'), $request->input('parent'));
        }

        // Otherwise the request is storing an uploaded file.
        return $this->storeFile($request, $entryInstance);
    }

    public function moveFiles(array $fileIds, ?string $targetId): void
    {
        // Check whether the authenticated user can move files.
        if ($this->policy()) {
            $this->gate->authorize('update', $this->model());
        }

        // Retrieve the target and the given files from the database.
        $target = !is_null($targetId) ? $this->queryBuilder()->key($targetId)->firstOrFail()->model() : null;
        $files = $this->queryBuilder()->key($fileIds)->get();


        $this->queryBuilder()
            ->getBuilder()
            ->whereKey($files->map(fn ($e) => $e->model()->getKey())->all())
            ->update(['parent_id' => optional($target)->getKey()]);
    }

    protected function queryItems(QueryBuilder $query): void
    {
        $query
            ->disableCache()
            ->orderBy('name', 'desc')
            ->when(request()->input('filter.types'), function (QueryBuilder $q, array $types) {
                $q->getBuilder()->where(function (Builder $q) use ($types) {
                    foreach ($types as $type) {
                        if (Str::endsWith($type, '/*')) {
                            $q->orWhere('mimetype', 'like', Str::replaceLast('/*', '', $type) . '%');
                            continue;
                        }

                        $q->orWhere('mimetype', $type);
                    }
                });
            });
    }

    /**
     * @param Request $request
     * @param EntryInstanceContract $entryInstance
     * @return EntryInstanceContract
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function storeFile(Request $request, EntryInstanceContract $entryInstance): EntryInstanceContract
    {
        /** @var PersistentAttachmentHandler $handler */
        $uploadedFile = $request->file('files.0');
        $filePath = $uploadedFile->store('files', Config::get('fastlane.attachments.disk'));

        $request->merge([
            'file' => $filePath,
            'extension' => FileFacade::extension($filePath),
            'mimetype' => $uploadedFile->getMimeType(),
            'size' => (string)$uploadedFile->getSize(),
            'parent_id' => $request->input('parent'),
            'is_active' => true,
        ]);

        // Validate the request data against the create fields
        // and save the validated data in a new variable.
        $fields = $entryInstance->schema()->getFields();

        $rules = Collection::make($fields)
            ->filter(fn(SchemaField $f) => $f instanceof WithRules)
            ->mapWithKeys(fn(WithRules $f) => $f->getCreateRules())
            ->all();

        $data = Validator::make($request->all(), $rules)->validated();

        // Pass the validated date through all fields, call hooks before saving,
        // then call more hooks after model has been saved.
        $this->hydrateFields($entryInstance, $fields, $data);
        $entryInstance->saveModel();

        return $entryInstance;
    }

    protected function makeDirectory(EntryInstanceContract $entryInstance, string $directory, ?string $parent = null)
    {
        Validator::make([
            'directory' => $directory,
            'parent_id' => $parent
        ], [
            'directory' => 'required|string',
            'parent_id' => ['nullable', Rule::exists('fastlane_files', 'id')->where('mimetype', 'fastlane/directory')],
        ])->validate();

        $data = [
            'file' => "{$parent}/{$directory}",
            'name' => $directory,
            'mimetype' => 'fastlane/directory',
            'extension' => 'DIR',
            'size' => 0,
            'is_active' => true,
            'parent_id' => $parent,
        ];

        // Validate the request data against the create fields
        // and save the validated data in a new variable.
        $fields = $entryInstance->schema()->getFields();

        $this->hydrateFields($entryInstance, $fields, $data);
        $entryInstance->saveModel();

        return $entryInstance;
    }
}
