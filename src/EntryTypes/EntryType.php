<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Models\Entry;
use CbtechLtd\Fastlane\Repositories\EntryRepository;
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
     * The database table that holds the entry type entities.
     * This property will be used by the Entry model to load the
     * registries from the proper table.
     *
     * @var string
     */
    protected static string $table;

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
    public static function entryRouteKey(Entry $model): string
    {
        return $model->getRouteKey() ?? '';
    }

    /**
     * @inheritDoc
     */
    public static function entryTitle(Entry $model): string
    {
        // Using route key as the title.
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

    public static function table(): string
    {
        if (isset(static::$table)) {
            return static::$table;
        }

        return Str::slug(
            Str::pluralStudly(Str::replaceLast('EntryType', '', static::key())), '_'
        );
    }

    /**
     * @inheritDoc
     */
    public static function repository(): EntryRepository
    {
        return once(fn () => app()->make(EntryRepository::class, ['entryType' => static::class]));
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
    protected static function installRolesAndPermissions(): void
    {
        $reflector = new ReflectionClass(static::class);
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
    protected static function installPermissions(Collection $permissions): void
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
