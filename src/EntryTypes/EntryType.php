<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\EntryTypes\Concerns\Resolvable;
use CbtechLtd\Fastlane\EntryTypes\Hooks\BeforeHydratingHook;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Concerns\HandlesHooks;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class EntryType implements EntryTypeContract
{
    use HandlesHooks, Resolvable;

    /** @description OnSavingHook */
    const HOOK_BEFORE_HYDRATING = 'beforeHydrating';
    /** @description OnSavingHook */
    const HOOK_BEFORE_CREATING = 'beforeCreating';
    /** @description OnSavingHook */
    const HOOK_BEFORE_UPDATING = 'beforeUpdating';
    /** @description OnSavingHook */
    const HOOK_BEFORE_SAVING = 'beforeSaving';
    /** @description OnSavingHook */
    const HOOK_AFTER_CREATING = 'afterCreating';
    /** @description OnSavingHook */
    const HOOK_AFTER_UPDATING = 'afterUpdating';
    /** @description OnSavingHook */
    const HOOK_AFTER_SAVING = 'afterSaving';

    protected array $hooks = [
        self::HOOK_BEFORE_HYDRATING => [],
        self::HOOK_BEFORE_CREATING  => [],
        self::HOOK_BEFORE_UPDATING  => [],
        self::HOOK_BEFORE_SAVING    => [],
        self::HOOK_AFTER_CREATING   => [],
        self::HOOK_AFTER_UPDATING   => [],
        self::HOOK_AFTER_SAVING     => [],
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
        /** @var ResourceType $name */
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

    public function fields(): array
    {
        return [];
    }

    public function makeModelTitle(Model $model): string
    {
        if (method_exists($model, 'toString')) {
            return $model->toString();
        }

        $field = Collection::make($this->fields())->flatMap(
            fn(SchemaField $f) => $f instanceof FieldPanel ? $f->getFields() : [$f]
        )->first();

        return $field->resolveValue($model)[$field->getName()];
    }

    public function install(): void
    {
        $this->installRolesAndPermissions();
    }

    public function isVisibleOnMenu(): bool
    {
        return true;
    }

    public function getItems(): EloquentCollection
    {
        $this->gate->authorize('list', $this->model());

        $query = $this
            ->newModelInstance()
            ->newModelQuery()
            ->orderBy('created_at', 'desc');

        $this->queryItems($query);

        return $query->get();
    }

    public function findItem(string $hashid): Model
    {
        $query = $this->newModelInstance()->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->whereHashid($hashid)->firstOrFail();

        $this->gate->authorize('show', $entry);

        return $entry;
    }

    public function store(EntryRequest $request): Model
    {
        $this->gate->authorize('create', $this->model());
        $entry = $this->newModelInstance();
        $fields = $this->schema()->getCreateFields();

        $this->hydrateFields(
            $request,
            $entry,
            $fields,
        );

        $beforeHook = new OnSavingHook($this, $entry, $request->validated());
        $this->executeHooks(static::HOOK_BEFORE_CREATING, $beforeHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $beforeHook);

        $beforeHook->model()->save();

        $afterHook = new OnSavingHook($this, $beforeHook->model(), $request->validated());
        $this->executeHooks(static::HOOK_AFTER_CREATING, $afterHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $afterHook);

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
            $this->schema()->getUpdateFields(),
        );

        $beforeHook = new OnSavingHook($this, $entry, $request->validated());
        $this->executeHooks(static::HOOK_BEFORE_UPDATING, $beforeHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $beforeHook);

        $beforeHook->model()->save();

        $afterHook = new OnSavingHook($this, $beforeHook->model(), $request->validated());
        $this->executeHooks(static::HOOK_AFTER_UPDATING, $afterHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $afterHook);

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

    public function newModelInstance(): Model
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

    protected function transformModelToSchema(): array
    {

    }

    /**
     * @param EntryRequest  $request
     * @param Model         $model
     * @param SchemaField[] $fields
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
                $field->fillModel($model, Arr::get($hookClass->data, $field->getName()), $request);
            }
        }
    }
}
