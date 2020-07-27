<?php

namespace CbtechLtd\Fastlane;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Http\Controllers\EntriesController;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuManager as MenuManagerContract;
use CbtechLtd\Fastlane\Support\Menu\MenuManager;
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
    protected MenuManagerContract $menuManager;

    public function __construct()
    {
        $this->registerEntryTypes();
        $this->registerMenuManager();
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

    public function getEntryTypeByIdentifier(string $identifier): EntryType
    {
        return $this->entryTypes->first(
            fn(EntryType $entryType) => $entryType->identifier() === $identifier
        );
    }

    public function getEntryTypeByClass(string $class): EntryType
    {
        return $this->entryTypes->first(
            fn(EntryType $entryType) => $entryType instanceof $class
        );
    }

    public function getMenuManager(): MenuManagerContract
    {
        return $this->menuManager;
    }

    protected function registerEntryTypes(): void
    {
        // Merge the user-defined types with the built-in types.
        // We don't add built-in types to the default config file because
        // it would error prone (devs could just remove it), but for now
        // we require all these built-in types to be correctly registered.
        $builtinTypes = [BackendUserEntryType::class];

        $classes = array_merge(config('fastlane.entry_types'), $builtinTypes);

        // Iterate over every entry type, instantiate it using the Laravel Container
        // and register their policies as well.
        $this->entryTypes = Collection::make($classes)->map(function ($typeClass) {
            /** @var EntryType $instance */
            $instance = app()->make($typeClass);

            if ($instance->policy()) {
                Gate::policy($instance->model(), $instance->policy());
            }

            return $instance;
        });
    }

    protected function registerMenuManager(): void
    {
        $this->menuManager = new MenuManager;
    }
}
