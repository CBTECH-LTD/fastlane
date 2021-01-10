<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Http\Controllers\EntriesController;
use CbtechLtd\Fastlane\Repositories\Repository;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class EntryType implements EntryTypeContract
{
    /**
     * An associative array containing the singular and plural label.
     * If not defined, it will be generated base on the class name.
     *
     * ```
     * [
     *      'singular' => 'User',
     *      'plural' => 'Users',
     * ]
     * ```
     *
     * @var array
     */
    protected static array $label;

    /**
     * An unique identifier for the entry type.
     *
     * @var string
     */
    protected static string $key;

    /**
     * The icon that should be displayed for the entry type.
     *
     * @var string|null
     */
    protected static ?string $icon = 'list';

    /**
     * The controller that should process requests to this entry type.
     *
     * @return string
     */
    protected static string $controller = EntriesController::class;

    /**
     * The repository that should be used to query the entry type.
     *
     * @return string
     */
    protected static string $repository;

    /**
     * Setup the entry type.
     *
     * @throws \ReflectionException
     */
    public static function boot(): void
    {
        $typeName = Str::replaceLast('EntryType', '', (new ReflectionClass(static::class))->getShortName());

        // Resolve the unique key.
        if (! isset(static::$key)) {
            static::$key = Str::kebab(Str::plural($typeName));
        }

        // Resolve labels.
        if (! isset(static::$label)) {
            $singularLabel = Str::title(Str::snake($typeName, ' '));

            static::$label = [
                'singular' => $singularLabel,
                'plural'   => Str::plural($singularLabel),
            ];
        }

        // TODO: Resolve repository.

        // TODO: Resolve controller.
    }

    /**
     * Determine the available route of the entry type.
     *
     * @return EntryTypeRouteCollection
     */
    public static function routes(): EntryTypeRouteCollection
    {
        return EntryTypeRouteCollection::make(static::key(), [
            EntryTypeRoute::get(static::class, '/', 'index'),
            EntryTypeRoute::get(static::class, '/new', 'create'),
            EntryTypeRoute::post(static::class, '/', 'store'),
            EntryTypeRoute::get(static::class, '/{id}', 'edit'),
            EntryTypeRoute::patch(static::class, '/{id}', 'update'),
            EntryTypeRoute::delete(static::class, '/{id}', 'delete'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function entryRouteKey(BaseModel $model): string
    {
        return $model->getRouteKey() ?? '';
    }

    /**
     * @inheritDoc
     */
    public static function entryTitle(BaseModel $model): string
    {
        // If the model has a toString method we just use it
        // to generate the title.
        if (method_exists($model, 'toString')) {
            return $model->toString();
        }

        // Model has no toString method, so we use its route key.
        // Hey fellow developer, it's not very readable!
        return static::entryRouteKey($model);
    }

    /**
     * @inheritDoc
     */
    public static function key(): string
    {
        return static::$key;
    }

    /**
     * @inheritDoc
     */
    public static function label(): array
    {
        return static::$label;
    }

    /**
     * @inheritDoc
     */
    public static function icon(): ?string
    {
        return static::$icon;
    }

    /**
     * @inheritDoc
     */
    public static function repository(): Repository
    {
        return once(fn() => app()->make(static::$repository, [static::class])->setEntryType(static::class));
    }

    /**
     * @inheritDoc
     */
    public static function controller(): string
    {
        return static::$controller;
    }

    /**
     * @inheritDoc
     */
    public static function install(): void
    {
        static::installRolesAndPermissions();
    }

    /**
     * Install roles and permissions defined in the entry type.
     *
     * @return void
     * @throws \ReflectionException
     */
    protected function installRolesAndPermissions(): void
    {
        $reflector = new ReflectionClass($this);
        $constants = Collection::make($reflector->getConstants());

        // Create permissions for all constants starting with PERM_.
        static::installPermissions(
            $constants->filter(function ($value, $key) {
                return Str::startsWith($key, 'PERM_');
            })
        );

        // Create roles for all constants starting with ROLE_.
        static::installRoles(
            $constants->filter(function ($value, $key) {
                return Str::startsWith($key, 'ROLE_');
            })
        );
    }

    /**
     * Install permissions defined in the entry type.
     *
     * @param Collection $permissions
     * @return void
     */
    protected function installPermissions(Collection $permissions): void
    {
        $permissions->each(function ($value) {
            Fastlane::createPermission($value);
        });
    }

    /**
     * Install roles defined in the entry type.
     *
     * @param Collection $roles
     * @return void
     */
    protected static function installRoles(Collection $roles): void
    {
        $roles->each(function ($value) {
            Fastlane::createRole($value);
        });
    }
}
