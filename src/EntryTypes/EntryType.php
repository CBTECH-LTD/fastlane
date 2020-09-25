<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Contracts\Panelizable;
use CbtechLtd\Fastlane\Contracts\QueryBuilder;
use CbtechLtd\Fastlane\Contracts\QueryBuilder as QueryBuilderContract;
use CbtechLtd\Fastlane\Contracts\Transformable;
use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\Exceptions\InvalidArgumentException;
use CbtechLtd\Fastlane\Exceptions\InvalidModelException;
use CbtechLtd\Fastlane\Exceptions\UnknownEventException;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Fields\Value;
use CbtechLtd\Fastlane\Http\Controllers\EntriesController;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class EntryType implements EntryTypeContract
{
    const EVENT_QUERY_LISTING = 'query-listing';
    const EVENT_QUERY_ENTRY = 'query-entry';
    const EVENT_CREATING = 'creating';
    const EVENT_CREATED = 'created';
    const EVENT_UPDATING = 'updating';
    const EVENT_UPDATED = 'updated';
    const EVENT_SAVING = 'saving';
    const EVENT_SAVED = 'saved';
    const EVENT_DELETING = 'deleting';
    const EVENT_DELETED = 'deleted';

    protected Model $entry;
    protected FieldCollection $fields;

    public function __construct(Model $model)
    {
        $this->entry = $model;
        $this->fields = (new FieldCollection(static::fields()))->setEntryType($this);
    }

    /**
     * Determine a unique identifier.
     *
     * @return string
     */
    public static function key(): string
    {
        $reflector = new ReflectionClass(static::class);
        $name = Str::replaceLast('EntryType', '', $reflector->getShortName());

        return Str::kebab(Str::plural($name));
    }

    /**
     * The model used by the entry type.
     *
     * @return string
     */
    public static function model(): string
    {
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass(static::class))->getName());

        if (! class_exists($name)) {
            ClassDoesNotExistException::model($name);
        }

        return $name;
    }

    /**
     * The singular name of the entry type.
     *
     * @return string
     */
    public static function name(): string
    {
        $reflector = new ReflectionClass(static::class);

        $name = $reflector->getShortName();

        return Str::title(Str::snake(Str::replaceLast('EntryType', '', $name), ' '));
    }

    /**
     * The plural name of the entry type.
     *
     * @return string
     */
    public static function pluralName(): string
    {
        return Str::plural(static::name());
    }

    /**
     * The icon to be used or empty for no icon.
     *
     * @return string
     */
    public static function icon(): string
    {
        return 'list';
    }

    /**
     * The authorization policy to be registered.
     *
     * @return string|null
     */
    public static function policy(): ?string
    {
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass(static::class))->getName()) . 'Policy';

        if (! class_exists($name)) {
            return null;
        }

        return $name;
    }

    /**
     * Determine the controller used by the entry type.
     *
     * @param Model|null $model
     * @return EntryTypeContract
     * @throws \Exception
     */
    public static function newInstance(?Model $model = null): EntryTypeContract
    {
        $modelClass = static::model();

        if ($model && ! is_a($model, $modelClass)) {
            throw new \Exception("Argument 1 expected {$modelClass} but got " . get_class($model));
        }

        return new static($model ?? new $modelClass);
    }

    /**
     * @inheritDoc
     */
    public static function boot(): void
    {
        static::bootModel();
    }

    /**
     * @return QueryBuilderContract
     * @throws \Exception
     */
    public static function query(): QueryBuilderContract
    {
        // By resolving the Query Builder instance using the Laravel IoC,
        // we enable developers to override the Query Builder implementation.
        $inst = static::newInstance();

        return app()->make(QueryBuilderContract::class, [
            'entryType' => static::newInstance(),
        ]);
    }

    /**
     * Get an instance of the entry type query builder
     * to fetch items to the listing page.
     *
     * @param callable|null $callback
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public static function queryListing(?callable $callback = null): LengthAwarePaginator
    {
        if (static::policy()) {
            Gate::authorize('index', static::model());
        }

        $query = static::query();

        if ($callback) {
            $query = call_user_func($callback, $query);
        }

        static::fireEvent(static::EVENT_QUERY_LISTING, [$query]);

        return $query->paginate();
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public static function queryEntry($id, ?callable $callback = null): ?EntryTypeContract
    {
        $query = static::query()->routeKey($id);

        if ($callback) {
            $query = call_user_func($callback, $id, $query);
        }

        return $query->first();
    }

    /**
     * Determine the controller used by the entry type.
     *
     * @return string
     */
    public static function controller(): string
    {
        return EntriesController::class;
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

    public static function install(): void
    {
        static::installRolesAndPermissions();
    }

    /**
     * Get the underlying Model instance.
     *
     * @return Model
     */
    public function modelInstance(): Model
    {
        return $this->entry;
    }

    /**
     * Get the key (ID) of the underlying model.
     *
     * @return mixed
     */
    public function entryKey()
    {
        return $this->entry->getKey();
    }

    /**
     * Generate a string to be used as the description
     * of the underlying model.
     *
     * @return string
     */
    public function entryTitle(): string
    {
        // If the model has a toString method we just use it
        // to generate the title.
        if (method_exists($this->entry, 'toString')) {
            return $this->entry->toString();
        }

        /**
         * Otherwise we just get the first field we find and
         * use it to generate the title.
         *
         * @var Field $field
         */
        $field = $this->getFields()->first(function (Field $field) {
            return ! $field instanceof Panelizable;
        });

        $value = ! is_null($field)
            ? $this->modelInstance()->getAttribute($field->getAttribute())
            : null;

        return $value ?? '';
    }

    /**
     * @inheritDoc
     * @return FieldCollection
     */
    public function getFields(): FieldCollection
    {
        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    public function store(array $data): EntryTypeContract
    {
        // Check whether the authenticated user can create
        // the given entry instance.
        if ($this->policy()) {
            Gate::authorize('create', static::model());
        }

        // Validate the request data against the update fields
        // and save the validated data in a new variable.
        $fields = $this->getFields()->onCreate();

        $data = $this->validateAndTransformData($data, $fields->getCreateRules($data));

        // Create the new model...
        $model = tap(app()->make(static::model()), function (Model $model) use ($data) {
            $model->fill($data);

            static::fireEvent(static::EVENT_CREATING, [$model, $data]);
            $model->save($data);
            static::fireEvent(static::EVENT_CREATED, [$model]);
        });


        return static::newInstance($model);
    }

    /**
     * @inheritDoc
     */
    public function update(array $data): self
    {
        // Check whether the authenticated user can update
        // the given entry instance.
        if (Gate::getPolicyFor(static::model())) {
            Gate::authorize('update', $this->modelInstance());
        }

        // Validate the request data against the update fields
        // and save the validated data in a new variable.
        $fields = $this->getFields()->onUpdate();

        $data = $this->validateAndTransformData($data, $fields);

        // Fill and save the model!
        $this->modelInstance()->fill($data)->save();

        return $this;
    }

    /**
     * Delete the instance of the underlying model.
     *
     * @return $this
     * @throws \Exception
     */
    public function delete(): self
    {
        // Check whether the authenticated user can delete
        // the given entry instance.
        if ($this->policy()) {
            Gate::authorize('delete', $this->modelInstance());
        }

        $this->modelInstance()->delete();

        return $this;
    }

    protected function validateAndTransformData(array $data, FieldCollection $fields): array
    {
        $data = Validator::make($data, $fields->getUpdateRules($data))->validated();

        return $fields->getCollection()->map(function (Field $field) use ($data) {
            if ($value = Arr::get($data, $field->getAttribute())) {
                if ($field instanceof Transformable) {
                    return $field->transformer()->fromRequest($this, $value);
                }

                return new Value($this, $value);
            }

            return null;
        })->filter()->all();
    }

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

    protected static function installPermissions(Collection $permissions): void
    {
        $permissions->each(function ($value) {
            Fastlane::createPermission($value);
        });
    }

    protected static function installRoles(Collection $roles): void
    {
        $roles->each(function ($value) {
            Fastlane::createRole($value);
        });
    }

    /**
     * Listen to an entry type event.
     *
     * @param string   $event
     * @param callable $callback
     * @throws UnknownEventException
     */
    protected static function listen(string $event, callable $callback): void
    {
        $keys = static::getEventKeys();

        if (! in_array($event, $keys)) {
            throw UnknownEventException::make($event);
        }

        Event::listen("fastlane.{$event}: " . static::class, $callback);
    }

    /**
     * Dispatch a entry type event.
     *
     * @param string $key
     * @param array  $params
     */
    protected static function fireEvent(string $key, array $params = []): void
    {
        $keys = static::getEventKeys();

        if (! in_array($key, $keys)) {
            throw UnknownEventException::make($key);
        }

        Event::until("fastlane.{$key}: " . static::class, $params);
    }

    /**
     * Get the available event keys.
     *
     * @return array
     */
    protected static function getEventKeys(): array
    {
        return array_values(
            (new ReflectionClass(static::class))->getConstants()
        );
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function bootModel(): void
    {
        // Check whether the model extends our BaseModel class
        // then set its entry type.
        if (! is_a(static::model(), BaseModel::class, true)) {
            InvalidModelException::invalid(static::model());
        }

        static::model()::withEntryType(static::class);

        // Check whether the entry type has set up a policy
        // to its model. Policies may be registered using
        // the default Laravel way as well.
        if (static::policy()) {
            Gate::policy(static::model(), static::policy());
        }
    }
}
