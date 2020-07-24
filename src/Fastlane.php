<?php

namespace CbtechLtd\Fastlane;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Http\Controllers\EntriesController;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Fastlane
{
    protected Collection $entryTypes;
    protected Collection $routes;
    protected array $flashMessages = [];

    public function __construct()
    {
        $this->registerEntryTypes();
    }

    public function flashSuccess(string $message, ?string $icon = null): void
    {
        $this->flashMessages[] = [
            'type'    => 'success',
            'message' => $message,
            'icon'    => $icon,
        ];

        session()->flash('fastlane-messages', $this->flashMessages);
    }

    public function flashAlert(string $message, ?string $icon = null): void
    {
        $this->flashMessages[] = [
            'type'    => 'alert',
            'message' => $message,
            'icon'    => $icon,
        ];

        session()->flash('fastlane-messages', $this->flashMessages);
    }

    public function flashDanger(string $message, ?string $icon = null): void
    {
        $this->flashMessages[] = [
            'type'    => 'danger',
            'message' => $message,
            'icon'    => $icon,
        ];

        session()->flash('fastlane-messages', $this->flashMessages);
    }

    public function getFlashMessages(): array
    {
        return $this->flashMessages;
    }

    public function createPermission(string $name): void
    {
        Permission::firstOrCreate(compact('name'));
    }

    public function createRole(string $name, array $permissions = ['*']): void
    {
        /** @var Role $role */
        $role = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => $name,
        ]);

        $role->syncPermissions(
            $permissions[0] === '*'
                ? Permission::all()
                : $permissions
        );
    }

    public function registerRoutes(Router $router): void
    {
        $this->entryTypes->each(function (EntryType $contentType) use ($router) {
            $router->fastlane($contentType->identifier(), EntriesController::class);
        });
    }

    public function entryTypes(): Collection
    {
        return $this->entryTypes;
    }

    protected function registerEntryTypes(): void
    {
        $defaultTypes = [BackendUserEntryType::class];
        $classes = array_merge(config('fastlane.entry_types'), $defaultTypes);

        $this->entryTypes = Collection::make($classes)->mapWithKeys(function ($typeClass) {
            /** @var EntryType $instance */
            $instance = app()->make($typeClass);

            if ($instance->policy()) {
                Gate::policy($instance->model(), $instance->policy());
            }

            return [$instance->identifier() => $instance];
        });
    }
}
