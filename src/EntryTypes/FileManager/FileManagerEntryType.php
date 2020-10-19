<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes\FileManager;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\Fields\Types\ActiveToggle;
use CbtechLtd\Fastlane\Fields\Types\Hidden;
use CbtechLtd\Fastlane\Fields\Types\Panel;
use CbtechLtd\Fastlane\Fields\Types\ShortText;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FileManagerEntryType extends EntryType
{
    const PERM_MANAGE_FILES = 'manage files';

    public static function key(): string
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

    public static function pluralName(): string
    {
        return __('fastlane::core.file_manager.plural_name');
    }

    public static function icon(): string
    {
        return 'image';
    }

    public static function fields(): array
    {
        return [
            ShortText::make('Name')->required()->sortable()->listable(),
            ShortText::make('Extension')->required()->sortable()->listable()->hideOnForm(),
            Hidden::make('Size')->listable(),
            Hidden::make('Mime Type', 'mimetype')->listable(),
            Hidden::make('URL')->computed()->listable(),
            ShortText::make('File')->required()->listable(),
            Panel::make('Settings')->withIcon('tools')->withFields([
                ActiveToggle::make(),
            ]),
        ];
    }

    public static function controller(): string
    {
        return FileManagerController::class;
    }

    public function store(array $data): EntryTypeContract
    {
        // Check whether the authenticated user can create an
        // instance of the given entry type.
        if ($this->policy()) {
            Gate::authorize('create', static::model());
        }

        $uploadedFile = data_get($data, 'files.0');
        $filePath = $uploadedFile->store('files', Config::get('fastlane.attachments.disk'));


        $data = array_merge($data, [
            'file'      => $filePath,
            'extension' => FileFacade::extension($filePath),
            'mimetype'  => $uploadedFile->getMimeType(),
            'size'      => (string)$uploadedFile->getSize(),
            'active'    => true,
        ]);

        // Validate the request data against the create fields
        // and save the validated data in a new variable.
        $fields = $this->getFields()->onCreate();

        $this->fillModel(
            $fields,
            $data = Validator::make($data, $fields->getCreateRules($data))->validated(),
        );

        // Dispatch events and save the model.
        static::fireEvent(static::EVENT_CREATING, [$this->modelInstance(), $data]);
        static::fireEvent(static::EVENT_SAVING, [$this->modelInstance(), $data]);

        $this->modelInstance()->save();

        static::fireEvent(static::EVENT_CREATED, [$this->modelInstance(), $data]);
        static::fireEvent(static::EVENT_SAVED, [$this->modelInstance(), $data]);

        return $this;
    }

    public static function queryListing(bool $paginate = true, ?callable $callback = null)
    {
        return parent::queryListing($paginate, function (QueryBuilder $query) {
            return $query->when(request()->input('filter.types'), function (QueryBuilder $q, $types) {
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
        });
    }

    protected static function menuGroup(): string
    {
        return __('fastlane::core.menu.system_group');
    }
}
