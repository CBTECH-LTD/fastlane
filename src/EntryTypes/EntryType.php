<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\EntryTypes\Concerns\Resolvable;
use CbtechLtd\Fastlane\EntryTypes\Hooks\BeforeHydratingHook;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\ApiResources\EntryResourceCollection;
use CbtechLtd\Fastlane\Support\Concerns\HandlesHooks;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
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

    public function apiResourceCollection(): string
    {
        /** @var ResourceType $name */
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass($this))->getName()) . 'ResourceCollection';

        if (class_exists($name)) {
            return $name;
        }

        return EntryResourceCollection::class;
    }

    public function policy(): ?string
    {
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass($this))->getName()) . 'Policy';

        if (! class_exists($name)) {
            return null;
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

    public function getItems(?Closure $queryCallback = null): EloquentCollection
    {
        $this->gate->authorize('list', $this->model());

        $query = $this
            ->newModelInstance()
            ->newModelQuery()
            ->orderBy('created_at', 'desc');

        $this->queryItems($query);

        if ($queryCallback) {
            call_user_func($queryCallback, $query);
        }

        return $query->get();
    }

    public function findItem(string $hashid, ?Closure $queryCallback = null): Model
    {
        $query = $this->newModelInstance()->newModelQuery();
        $this->querySingleItem($query, $hashid);

        if ($queryCallback) {
            call_user_func($queryCallback, $query);
        }

        $entry = $query->whereHashid($hashid)->firstOrFail();

        $this->gate->authorize('show', $entry);

        return $entry;
    }

    public function store(Request $request): Model
    {
        $this->gate->authorize('create', $this->model());
        $entry = $this->newModelInstance();
        $fields = $this->schema()->getCreateFields();

        // Check whether the authenticated user can update
        // the given entry instance.
        if ($this->policy()) {
            $this->gate->authorize('create', $entry);
        }

        // Validate the request data against the update fields
        // and save the validated data in a new variable.
        $fields = $this->schema()->getCreateFields();

        $rules = Collection::make($fields)
            ->mapWithKeys(fn(SchemaField $fieldType) => $fieldType->getCreateRules())
            ->all();

        $data = Validator::make($request->all(), $rules)->validated();

        // Pass the validated date through all fields, call hooks before saving,
        // then call more hooks after model has been saved.
        $this->hydrateFields($entry, $fields, $data);

        $beforeHook = new OnSavingHook($this, $entry, $data);
        $this->executeHooks(static::HOOK_BEFORE_CREATING, $beforeHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $beforeHook);

        $beforeHook->model()->save();

        $afterHook = new OnSavingHook($this, $beforeHook->model(), $data);
        $this->executeHooks(static::HOOK_AFTER_CREATING, $afterHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $afterHook);

        return $afterHook->model();
    }

    public function update(Request $request, string $hashid): Model
    {
        $query = $this->newModelInstance()->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->whereHashid($hashid)->firstOrFail();

        // Check whether the authenticated user can update
        // the given entry instance.
        if ($this->policy()) {
            $this->gate->authorize('update', $entry);
        }

        // Validate the request data against the update fields
        // and save the validated data in a new variable.
        $fields = $this->schema()->getUpdateFields();

        $rules = Collection::make($fields)
            ->mapWithKeys(fn(SchemaField $fieldType) => $fieldType->getUpdateRules())
            ->all();

        $data = Validator::make($request->all(), $rules)->validated();

        // Pass the validated date through all fields, call hooks before saving,
        // then call more hooks after model has been saved.
        $this->hydrateFields($entry, $fields, $data);

        $beforeHook = new OnSavingHook($this, $entry, $data);
        $this->executeHooks(static::HOOK_BEFORE_UPDATING, $beforeHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $beforeHook);

        $beforeHook->model()->save();

        $afterHook = new OnSavingHook($this, $beforeHook->model(), $data);
        $this->executeHooks(static::HOOK_AFTER_UPDATING, $afterHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $afterHook);

        return $afterHook->model();
    }

    public function delete(string $hashid): Model
    {
        $query = $this->newModelInstance()->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->whereHashid($hashid)->firstOrFail();

        if ($this->policy()) {
            $this->gate->authorize('delete', $entry);
        }

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
     * @param Model         $model
     * @param SchemaField[] $fields
     * @param array         $data
     */
    protected function hydrateFields(Model $model, array $fields, array $data): void
    {
        $hookClass = new BeforeHydratingHook($this, $model, $fields, $data);

        $this->executeHooks(
            static::HOOK_BEFORE_HYDRATING,
            $hookClass
        );

        foreach ($fields as $field) {
            if (Arr::has($hookClass->data, $field->getName())) {
                $field->fillModel($model, Arr::get($hookClass->data, $field->getName()), $data);
            }
        }
    }
}
