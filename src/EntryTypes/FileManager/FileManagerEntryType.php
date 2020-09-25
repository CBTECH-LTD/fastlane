<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Contracts\WithCollectionLinks;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomController;
use CbtechLtd\Fastlane\Contracts\WithCustomViews;
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

class FileManagerEntryType extends EntryType implements WithCustomViews, WithCollectionLinks, WithCustomController
{
    const PERM_MANAGE_FILES = 'manage files';

    public function identifier(): string
    {
        return __('fastlane::core.file_manager.identifier');
    }

    public static function model(): string
    {
        return File::class;
    }

    public static function name(): string
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
            StringField::make('name', 'Name')
                ->required()
                ->showOnIndex()
                ->sortable(),

            StringField::make('extension', 'Extension')
                ->maxLength(10)
                ->showOnIndex()
                ->hideOnForm()
                ->sortable(),

            HiddenField::make('size', 'Size')->showOnIndex(),

            HiddenField::make('mimetype', 'Mime Type')->showOnIndex(),

            HiddenField::make('url', 'URL')
                ->showOnIndex()
                ->readValueUsing(function (EntryInstanceContract $entryInstance) {
                    return new FieldValue('url', $entryInstance->model()->url());
                }),

            StringField::make('file', 'File')
                ->showOnIndex()
                ->required(),

            FieldPanel::make('Settings')->withIcon('tools')
                ->withFields([
                    ToggleField::make('is_active', 'Active')
                        ->required()
                        ->showOnIndex(),
                ]),
        ];
    }

    public function getListingView(): ?string
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

        /** @var PersistentAttachmentHandler $handler */
        $uploadedFile = $request->file('files.0');
        $filePath = $uploadedFile->store('files', Config::get('fastlane.attachments.disk'));

        $request->merge([
            'file'      => $filePath,
            'extension' => FileFacade::extension($filePath),
            'mimetype'  => $uploadedFile->getMimeType(),
            'size'      => (string)$uploadedFile->getSize(),
            'active'    => true,
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

    protected function queryItems(QueryBuilder $query): void
    {
        $query->when(request()->input('filter.types'), function (QueryBuilder $q, $types) {
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
}
