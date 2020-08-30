<?php

namespace CbtechLtd\Fastlane;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\EntryTypes\Content\ContentEntryType;
use CbtechLtd\Fastlane\EntryTypes\FileManager\FileManagerEntryType;
use CbtechLtd\Fastlane\Exceptions\EntryTypeNotRegisteredException;
use CbtechLtd\Fastlane\Http\Controllers;
use CbtechLtd\Fastlane\Http\Requests\FastlaneRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\WithCustomController;
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
    protected FastlaneRequest $request;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(): void
    {
        $this->registerEntryTypes();
        $this->registerMenuManager();
    }

    public function setRequest(FastlaneRequest $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest(): FastlaneRequest
    {
        return $this->request;
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

    public function createPermission(string $name, string $guard = 'fastlane-cp'): void
    {
        Permission::firstOrCreate([
            'name'       => $name,
            'guard_name' => $guard,
        ]);
    }

    public function createRole(string $name, array $permissions = ['*'], $guard = 'fastlane-cp'): void
    {
        /** @var Role $role */
        $role = Role::firstOrCreate([
            'name'       => $name,
            'guard_name' => $guard,
        ]);

        $role->syncPermissions(
            $permissions[0] === '*'
                ? Permission::where('guard_name', $guard)->get()
                : $permissions
        );
    }

    public function registerControlPanelRoutes(Router $router): void
    {
        $this->entryTypes->each(function (EntryType $entryType) use ($router) {
            $controller = $entryType instanceof WithCustomController
                ? $entryType->getController()
                : Controllers\EntriesController::class;

            $router
                ->middleware('fastlane.resolve:control_panel')
                ->group(fn() => $router->fastlaneControlPanel(
                    $entryType->identifier(),
                    $controller
                ));
        });
    }

    public function registerApiRoutes(Router $router): void
    {
        $this->entryTypes->each(function (EntryType $entryType) use ($router) {
            $router
                ->middleware('fastlane.resolve:api')
                ->group(fn() => $router->fastlaneContentApi(
                    $entryType->identifier(),
                    Controllers\API\EntriesController::class
                ));
        });
    }

    public function entryTypes(): Collection
    {
        return $this->entryTypes;
    }

    public function getEntryTypeByIdentifier(string $identifier): EntryType
    {
        $item = $this->entryTypes->first(
            fn(EntryType $entryType) => $entryType->identifier() === $identifier
        );

        if (! $item) {
            throw new EntryTypeNotRegisteredException;
        }

        return $item;
    }

    public function getEntryTypeByClass(string $class): EntryType
    {
        return $this->entryTypes->first(
            fn(EntryType $entryType) => $entryType instanceof $class
        );
    }

    public function getAccessTokenAbilities(): array
    {
        return [
            'entries:read',
        ];
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
        $builtinTypes = [
            ContentEntryType::class,
            FileManagerEntryType::class,
            BackendUserEntryType::class,
        ];

        $classes = array_merge(config('fastlane.entry_types'), $builtinTypes);

        // Iterate over every entry type, instantiate a base instance of  it using
        // the Laravel Container and register their policies as well.
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
