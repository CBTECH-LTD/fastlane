<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\EntryTypes\Hooks\BeforeHydratingHook;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\QueryFilter\QueryFilter;
use CbtechLtd\Fastlane\QueryFilter\QueryFilterContract;
use CbtechLtd\Fastlane\Support\ApiResources\EntryResource;
use CbtechLtd\Fastlane\Support\ApiResources\EntryResourceCollection;
use CbtechLtd\Fastlane\Support\Concerns\HandlesHooks;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithRules;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WriteValue;
use CbtechLtd\JsonApiTransformer\ApiResources\ResourceType;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class EntryType implements EntryTypeContract
{
    use HandlesHooks, QueriesForContentAPI;

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

    public static function load(): EntryTypeContract
    {
        return app('fastlane')->getEntryTypeByClass(static::class);
    }

    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    public function newInstance(?Model $model): EntryInstanceContract
    {
        return new EntryInstance($this, $model ?? $this->newModelInstance());
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
            return EntryResource::class;
        }

        return $name;
    }

    public function apiResourceCollection(): string
    {
        /** @var ResourceType $name */
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass($this))->getName()) . 'ResourceCollection';

        if (! class_exists($name)) {
            return EntryResourceCollection::class;
        }

        return $name;
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

    public function install(): void
    {
        $this->installRolesAndPermissions();
    }

    public function isVisibleOnMenu(): bool
    {
        return true;
    }

    public function getItemsForRelationField(?QueryFilterContract $queryFilter = null): Collection
    {
        $this->gate->authorize('list', $this->model());

        $query = ($queryFilter ?? new QueryFilter)
            ->addOrder('created_at')
            ->addOrder('id')
            ->pipeThrough(
                $this->newModelInstance()->newModelQuery()
            );

        $this->queryItemsForRelationField($query);

        return $query->get()->map(fn(Model $model) => $this->newInstance($model));
    }

    public function getItems(?QueryFilterContract $queryFilter = null): LengthAwarePaginator
    {
        $this->gate->authorize('list', $this->model());

        $this->prepareQueryFilter($queryFilter ?? new QueryFilter);

        $query = $queryFilter
            ->addOrder('created_at')
            ->addOrder('id')
            ->pipeThrough(
                $this->newModelInstance()->newModelQuery()
            );

        $this->queryItems($query);

        $paginator = $query->paginate($this->getItemsPerPage());
        $paginator->getCollection()->transform(fn(Model $model) => $this->newInstance($model));

        return $paginator;
    }

    public function findItem(string $hashid): EntryInstanceContract
    {
        $model = $this->newModelInstance();
        $query = $model->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->where($model->getRouteKeyName(), $hashid)->firstOrFail();

        $this->gate->authorize('show', $entry);

        return $this->newInstance($entry);
    }

    public function store(Request $request): EntryInstanceContract
    {
        // Check whether the authenticated user can create an
        // instance of the given entry type.
        if ($this->policy()) {
            $this->gate->authorize('create', $this->model());
        }

        $entryInstance = $this->newInstance(null);

        // Validate the request data against the create fields
        // and save the validated data in a new variable.
        $fields = $entryInstance->schema()->getCreateFields();

        $rules = Collection::make($fields)
            ->filter(fn(SchemaField $f) => $f instanceof WithRules)
            ->mapWithKeys(fn(WithRules $f) => $f->getCreateRules())
            ->all();

        $data = Validator::make($request->all(), $rules)->validated();

        // Pass the validated date through all fields, call hooks before saving,
        // then call more hooks after model has been saved.
        $this->hydrateFields($entryInstance, $fields, $data);

        $beforeHook = new OnSavingHook($entryInstance, $data);
        $this->executeHooks(static::HOOK_BEFORE_CREATING, $beforeHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $beforeHook);

        $beforeHook->entryInstance()->saveModel();

        $afterHook = new OnSavingHook($beforeHook->entryInstance(), $data);
        $this->executeHooks(static::HOOK_AFTER_CREATING, $afterHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $afterHook);

        return $afterHook->entryInstance();
    }

    public function update(Request $request, string $hashid): EntryInstanceContract
    {
        $model = $this->newModelInstance();
        $query = $model->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->where($model->getRouteKeyName(), $hashid)->firstOrFail();

        // Check whether the authenticated user can update
        // the given entry instance.
        if ($this->policy()) {
            $this->gate->authorize('update', $entry);
        }

        // Create an Entry Instance based in the model we have
        // just found in the database.
        $entryInstance = $this->newInstance($entry);

        // Validate the request data against the update fields
        // and save the validated data in a new variable.
        $fields = $entryInstance->schema()->getUpdateFields();

        $rules = Collection::make($fields)
            ->filter(fn(SchemaField $f) => $f instanceof WithRules)
            ->mapWithKeys(fn(WithRules $f) => $f->getUpdateRules())
            ->all();

        $data = Validator::make($request->all(), $rules)->validated();

        // Pass the validated date through all fields, call hooks before saving,
        // then call more hooks after model has been saved.
        $this->hydrateFields($entryInstance, $fields, $data);

        $beforeHook = new OnSavingHook($entryInstance, $data);
        $this->executeHooks(static::HOOK_BEFORE_UPDATING, $beforeHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $beforeHook);

        $beforeHook->entryInstance()->saveModel();

        $afterHook = new OnSavingHook($beforeHook->entryInstance(), $data);
        $this->executeHooks(static::HOOK_AFTER_UPDATING, $afterHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $afterHook);

        return $afterHook->entryInstance();
    }

    public function delete(string $hashid): EntryInstanceContract
    {
        $model = $this->newModelInstance();
        $query = $model->newModelQuery();
        $this->querySingleItem($query, $hashid);
        $entry = $query->where($model->getRouteKeyName(), $hashid)->firstOrFail();

        if ($this->policy()) {
            $this->gate->authorize('delete', $entry);
        }

        $entry->delete();

        return $this->newInstance($entry);
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

    protected function queryItemsForRelationField(Builder $query): void
    {
        //
    }

    protected function prepareQueryFilter(QueryFilterContract $queryFilter): void
    {

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
     * @param EntryInstanceContract $entryInstance
     * @param SchemaField[]         $fields
     * @param array                 $data
     */
    protected function hydrateFields(EntryInstanceContract $entryInstance, array $fields, array $data): void
    {
        $hookClass = new BeforeHydratingHook($entryInstance, $fields, $data);

        $this->executeHooks(
            static::HOOK_BEFORE_HYDRATING,
            $hookClass
        );

        Collection::make($fields)
            ->each(function (SchemaField $field) use ($hookClass, $entryInstance, $data) {
                if ($field instanceof WriteValue) {
                    if (Arr::has($hookClass->data, $field->getName())) {
                        $field->writeValue($entryInstance, Arr::get($hookClass->data, $field->getName()), $data);
                    }
                }
            });
    }

    protected function getItemsPerPage(): int
    {
        return config('fastlane.control_panel.pagination_per_page');
    }
}
