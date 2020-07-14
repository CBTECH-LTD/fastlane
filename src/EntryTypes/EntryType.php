<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\EntryTypes\Hooks\BeforeHydratingHook;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use CbtechLtd\Fastlane\Support\HandlesHooks;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResourceCollection;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class EntryType implements EntryTypeContract
{
    use HandlesHooks;

    /** Parameters: EntryRequest $request, Model $entry, array $fields, array $data */
    const HOOK_BEFORE_HYDRATING = 'beforeHydrating';
    /** Parameters: Model $entry */
    const HOOK_BEFORE_CREATING = 'beforeCreating';
    /** Parameters: Model $entry */
    const HOOK_BEFORE_UPDATING = 'beforeUpdating';
    /** Parameters: Model $entry */
    const HOOK_AFTER_CREATING = 'afterCreating';
    /** Parameters: Model $entry */
    const HOOK_AFTER_UPDATING = 'afterUpdating';

    protected array $hooks = [
        self::HOOK_BEFORE_HYDRATING => [],
        self::HOOK_BEFORE_CREATING  => [],
        self::HOOK_BEFORE_UPDATING  => [],
        self::HOOK_AFTER_CREATING   => [],
        self::HOOK_AFTER_UPDATING   => [],
    ];

    protected Gate $gate;

    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    public function name(): string
    {
        $reflector = new ReflectionClass($this);

        $name = $reflector->getShortName();

        return Str::title(Str::snake(Str::replaceLast('EntryType', '', $name), ' '));
    }

    public function pluralName(): string
    {
        return Str::plural($this->name());
    }

    public function identifier(): string
    {
        $reflector = new ReflectionClass($this);
        $name = Str::replaceLast('EntryType', '', $reflector->getShortName());

        return Str::kebab(Str::plural($name));
    }

    public function icon(): string
    {
        return 'list';
    }

    public function model(): string
    {
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass($this))->getName());

        if (! class_exists($name)) {
            ClassDoesNotExistException::model($name);
        }

        return $name;
    }

    public function apiResource(): string
    {
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass($this))->getName()) . 'Resource';

        if (! class_exists($name)) {
            ClassDoesNotExistException::model($name);
        }

        return $name;
    }

    public function policy(): ?string
    {
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass($this))->getName()) . 'Policy';

        if (! class_exists($name)) {
            ClassDoesNotExistException::model($name);
        }

        return $name;
    }

    public function schema(): EntrySchema
    {
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass($this))->getName()) . 'Schema';

        if (! class_exists($name)) {
            ClassDoesNotExistException::model($name);
        }

        return new $name($this);
    }

    public function install(): void
    {
        $this->installRolesAndPermissions();
    }

    public function isVisibleOnMenu(): bool
    {
        return true;
    }

    public function getItems(): ApiResourceCollection
    {
        $this->gate->authorize('list', $this->model());

        $query = $this->newModelInstance()->newModelQuery();
        $this->queryItems($query);
        $items = $query->get();

        return $this->apiResource()::collection($items);
    }

    public function findItem(string $hashid): ApiResource
    {
        $query = $this->newModelInstance()->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->whereHashid($hashid)->firstOrFail();

        $this->gate->authorize('show', $entry);

        return $this->apiResource()::single($entry);
    }

    public function store(EntryRequest $request): Model
    {
        $this->gate->authorize('create', $this->model());
        $entry = $this->newModelInstance();

        $this->hydrateFields(
            $request,
            $entry,
            $this->schema()->getDefinition()->toCreate()->getFields(),
        );

        $beforeHook = new OnSavingHook($this, $entry, $request->validated());
        $this->executeHooks(static::HOOK_BEFORE_CREATING, $beforeHook);

        $beforeHook->model()->save();

        $afterHook = new OnSavingHook($this, $beforeHook->model(), $request->validated());
        $this->executeHooks(static::HOOK_AFTER_CREATING, $afterHook);

        return $afterHook->model();
    }

    public function update(EntryRequest $request, string $hashid): Model
    {
        $query = $this->newModelInstance()->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->whereHashid($hashid)->firstOrFail();

        $this->gate->authorize('update', $entry);

        $this->hydrateFields(
            $request,
            $entry,
            $this->schema()->getDefinition()->toUpdate()->getFields(),
        );

        $beforeHook = $this->executeHooks(static::HOOK_BEFORE_UPDATING, new OnSavingHook($this, $entry, $request->validated()));
        $beforeHook->model()->save();

        $afterHook = $this->executeHooks(static::HOOK_AFTER_UPDATING, new OnSavingHook($this, $beforeHook->model(), $request->validated()));
        return $afterHook->model();
    }

    public function delete(string $hashid): Model
    {
        $query = $this->newModelInstance()->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->whereHashid($hashid)->firstOrFail();

        $this->gate->authorize('delete', $entry);

        $entry->delete();

        return $entry;
    }

    protected function newModelInstance(): Model
    {
        return app()->make($this->model());
    }

    protected function installRolesAndPermissions(): void
    {
        $reflector = new ReflectionClass($this);
        $constants = Collection::make($reflector->getConstants());

        // Create permissions for all constants starting with PERM_.
        $this->installPermissions(
            $constants->filter(function ($value, $key) {
                return Str::startsWith($key, 'PERM_');
            })
        );

        // Create roles for all constants starting with ROLE_.
        $this->installRoles(
            $constants->filter(function ($value, $key) {
                return Str::startsWith($key, 'ROLE_');
            })
        );
    }

    protected function installPermissions(Collection $permissions): void
    {
        $permissions->each(function ($value) {
            FastlaneFacade::createPermission($value);
        });
    }

    protected function installRoles(Collection $roles): void
    {
        $roles->each(function ($value) {
            FastlaneFacade::createRole($value);
        });
    }

    protected function queryItems(Builder $query): void
    {
        //
    }

    protected function querySingleItem(Builder $query, string $hashid): void
    {
        //
    }

    /**
     * @param EntryRequest      $request
     * @param Model             $model
     * @param SchemaFieldType[] $fields
     */
    protected function hydrateFields(EntryRequest $request, Model $model, array $fields): void
    {
        $hookClass = new BeforeHydratingHook($this, $request, $model, $fields, $request->validated());

        $this->executeHooks(
            static::HOOK_BEFORE_HYDRATING,
            $hookClass
        );

        foreach ($fields as $field) {
            if (Arr::has($hookClass->data, $field->getName())) {
                $field->hydrateValue($model, Arr::get($hookClass->data, $field->getName()), $request);
            }
        }
    }
}
