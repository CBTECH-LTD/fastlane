<?php

namespace CbtechLtd\Fastlane;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Fastlane
{
    protected static array $translations = [];

    /**
     * Determine whether there's an user authenticated
     * in the control panel.
     *
     * @return bool
     */
    public static function isAuthenticated(): bool
    {
        return Auth::guard('fastlane-cp')->check();
    }

    /**
     * Get the authenticated user in control panel.
     *
     * @return Authenticatable|null
     */
    public static function user(): ?Authenticatable
    {
        return Auth::guard('fastlane-cp')->user();
    }

    /**
     * Add a flash message to the session.
     *
     * @param string      $type
     * @param string      $message
     * @param string|null $icon
     */
    public static function flashMessage(string $type, string $message, ?string $icon = null): void
    {
        $newMsg = (object)compact('type', 'message', 'icon');

        $msgs = array_merge(session()->get('fastlane-messages', []), [$newMsg]);

        Livewire::dispatch('fastlaneMessageAdded', $newMsg);
        session()->flash('fastlane-messages', $msgs);
    }

    /**
     * Add a success message to the session.
     *
     * @param string      $message
     * @param string|null $icon
     */
    public static function flashSuccess(string $message, ?string $icon = null): void
    {
        static::flashMessage('success', $message, $icon);
    }

    /**
     * Add an alert message to the session.
     *
     * @param string      $message
     * @param string|null $icon
     */
    public static function flashAlert(string $message, ?string $icon = null): void
    {
        static::flashMessage('alert', $message, $icon);
    }

    /**
     * Add a danger message to the session.
     *
     * @param string      $message
     * @param string|null $icon
     */
    public static function flashDanger(string $message, ?string $icon = null): void
    {
        static::flashMessage('danger', $message, $icon);
    }

    /**
     * Get all flash messages in the session.
     *
     * @return array
     */
    public static function getFlashMessages(): array
    {
        return session()->get('fastlane-messages', []);
    }

    /**
     * Build a full route name for Fastlane Control Panel
     * based on the given name.
     *
     * @param string $name
     * @return string
     */
    public static function makeCpRouteName(string $name): string
    {
        return "fastlane.cp.{$name}";
    }

    /**
     * Return a route for the Fastlane Control Panel based on
     * the given route name.
     *
     * @param string $name
     * @param null   $params
     * @param bool   $absolute
     * @return string
     */
    public static function cpRoute(string $name, $params = null, bool $absolute = true): string
    {
        return route(static::makeCpRouteName($name), $params, $absolute);
    }

    /**
     * @param string $name
     * @param string $guard
     */
    public static function createPermission(string $name, string $guard = 'fastlane-cp'): void
    {
        Permission::firstOrCreate([
            'name'       => $name,
            'guard_name' => $guard,
        ]);
    }

    /**
     * @param string         $name
     * @param array|string[] $permissions
     * @param string         $guard
     */
    public static function createRole(string $name, array $permissions = ['*'], $guard = 'fastlane-cp'): void
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

    /**
     * @param Router $router
     */
    public static function registerControlPanelRoutes(Router $router): void
    {
        foreach (Facades\EntryType::all() as $entryType) {
            $router->fastlaneControlPanel($entryType::key(), $entryType::routes());
        }
    }

    /**
     * Get a list of abilities registered for the Fastlane API.
     *
     * @return string[]
     */
    public static function getAccessTokenAbilities(): array
    {
        return [
            'entries:read',
        ];
    }

    /**
     * Get an associative array with the translations to be
     * used in the Control Panel frontend.
     *
     * @return array
     */
    public static function getTranslations(): array
    {
        return [
            'core' => Lang::get('fastlane::core'),
        ];
    }
}
