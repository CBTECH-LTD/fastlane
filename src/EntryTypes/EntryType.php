<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Contracts\Panelizable;
use CbtechLtd\Fastlane\Contracts\QueryBuilder;
use CbtechLtd\Fastlane\Contracts\QueryBuilder as QueryBuilderContract;
use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\Exceptions\InvalidModelException;
use CbtechLtd\Fastlane\Exceptions\UnknownEventException;
use CbtechLtd\Fastlane\Fastlane;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Fields\UndefinedValue;
use CbtechLtd\Fastlane\Fields\ValueResolver;
use CbtechLtd\Fastlane\Http\Controllers\EntriesController;
use CbtechLtd\Fastlane\Http\Transformers\EntryResource;
use CbtechLtd\Fastlane\Support\Eloquent\BaseModel;
use CbtechLtd\Fastlane\Support\Eloquent\Concerns\Hashable;
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
        $name = Str::replaceLast('EntryType', '', (new ReflectionClass(static::class))->getShortName()) . 'Policy';

        $fullClass = 'App\\Policies\\' . $name;

        if (! class_exists($fullClass)) {
            return null;
        }

        return $fullClass;
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
        return app()->make(QueryBuilderContract::class, [
            'entryType' => static::newInstance(),
        ]);
    }

    /**
     * Get an instance of the entry type query builder
     * to fetch items to the listing page.
     *
     * @param bool          $paginate
     * @param callable|null $callback
     * @return LengthAwarePaginator|Collection
     * @throws UnknownEventException
     */
    public static function queryListing(bool $paginate = true, ?callable $callback = null)
    {
        if (static::policy()) {
            Gate::authorize('index', static::model());
        }

        // Start the query, selecting only fields we really need.
        $defaultColumns = ['id'];

        if (in_array(Hashable::class, class_uses_recursive(static::model()))) {
            $defaultColumns[] = 'hashid';
        }

        $columns = static::newInstance()->getFields()->onListing()
            ->getAttributes()->filter(fn(Field $field) => ! $field->isComputed())
            ->keys()->all();

        $query = static::query()
            ->select(array_merge(
                $defaultColumns,
                $columns,
            ));

        if ($callback) {
            $query = call_user_func($callback, $query);
        }

        static::fireEvent(static::EVENT_QUERY_LISTING, [$query]);

        return $paginate ? $query->paginate() : $query->get();
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
     * Get the route key of the underlying model.
     *
     * @return mixed
     */
    public function entryRouteKey()
    {
        return $this->entry->getRouteKey();
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

        // Validate the request data against the required fields
        // and fill it to the model.
        $fields = $this->getFields()->onCreate();

        $this->fillModel(
            $fields,
            $data = Validator::make($data, $fields->getCreateRules($data))->validated()
        );

        // Dispatch events and save the model.
        static::fireEvent(static::EVENT_CREATING, [$this->modelInstance(), $data]);
        static::fireEvent(static::EVENT_SAVING, [$this->modelInstance(), $data]);

        $this->modelInstance()->save();

        static::fireEvent(static::EVENT_CREATED, [$this->modelInstance(), $data]);
        static::fireEvent(static::EVENT_SAVED, [$this->modelInstance(), $data]);

        return $this;
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

        $this->fillModel(
            $fields,
            $data = Validator::make($data, $fields->getUpdateRules($data))->validated()
        );

        // Dispatch events and save the model
        static::fireEvent(static::EVENT_UPDATING, [$this->modelInstance(), $data]);
        static::fireEvent(static::EVENT_SAVING, [$this->modelInstance(), $data]);

        $this->modelInstance()->save();

        static::fireEvent(static::EVENT_UPDATED, [$this->modelInstance(), $data]);
        static::fireEvent(static::EVENT_SAVED, [$this->modelInstance(), $data]);

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

        static::fireEvent(static::EVENT_DELETING, [$this->modelInstance()]);
        $this->modelInstance()->delete();
        static::fireEvent(static::EVENT_DELETED, [$this->modelInstance()]);

        return $this;
    }

    /**
     * Get an EntryResource instance representing this entry type instance.
     *
     * @return EntryResource
     */
    public function toResource(): EntryResource
    {
        return new EntryResource($this);
    }

    /**
     * Get an array representation of the entry type including
     * only fields available on listing page.
     *
     * @param bool $includeSchema
     * @return EntryResource
     */
    public function toListingResource(bool $includeSchema = false): EntryResource
    {
        return EntryResource::toListing($this);
    }

    /**
     * Get an array representation of the entry type including
     * only fields available on create page.
     *
     * @param bool $includeSchema
     * @return EntryResource
     */
    public function toCreateResource(bool $includeSchema = false): EntryResource
    {
        return EntryResource::toCreate($this);
    }

    /**
     * Get an array representation of the entry type including
     * only fields available on update page.
     *
     * @param bool $includeSchema
     * @return EntryResource
     */
    public function toUpdateResource(bool $includeSchema = false): EntryResource
    {
        return EntryResource::toUpdate($this);
    }

    protected function fillModel(FieldCollection $fields, array $data): self
    {
        $fields->each(function (Field $field) use ($data) {
            if ($field->isComputed()) {
                return;
            }

            if (Arr::has($data, $field->getAttribute())) {
                $value = $field->write(Arr::get($data, $field->getAttribute()), $this);

                if (! $value instanceof UndefinedValue) {
                    $this->modelInstance()->{$field->getAttribute()} = $value;
                }
            }
        });

        return $this;
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
    public static function listen(string $event, callable $callback): void
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
