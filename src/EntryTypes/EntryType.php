<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResource;
use CbtechLtd\JsonApiTransformer\ApiResources\ApiResourceCollection;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class EntryType implements EntryTypeContract
{
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

        return new $name;
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

        $items = $this->model()::all();

        return $this->apiResource()::collection($items);
    }

    public function findItem(string $hashid): ApiResource
    {
        $entry = $this->model()::findHashid($hashid);

        $this->gate->authorize('show', $entry);

        return $this->apiResource()::single($entry);
    }

    public function store(array $data): Model
    {
        $this->gate->authorize('create', $this->model());

        return tap($this->newModelInstance(), function (Model $entry) use ($data) {
            $entry->fill($data)->save();
        });
    }

    public function update(string $hashid, array $data): Model
    {
        $entry = $this->model()::findHashid($hashid);
        $this->gate->authorize('update', $entry);

        $entry->fill($data)->save();

        return $entry;
    }

    public function delete(string $hashid): Model
    {
        $entry = $this->model()::findHashid($hashid);
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
}
