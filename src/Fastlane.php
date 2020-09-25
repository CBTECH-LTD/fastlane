<?php

namespace CbtechLtd\Fastlane;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Lang;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Fastlane
{
    protected static array $flashMessages = [];
    protected static array $translations = [];

    /**
     * Add a success message to the session.
     *
     * @param string      $message
     * @param string|null $icon
     */
    public static function flashSuccess(string $message, ?string $icon = null): void
    {
        static::$flashMessages[] = [
            'type'    => 'success',
            'message' => $message,
            'icon'    => $icon,
        ];

        session()->flash('fastlane-messages', static::$flashMessages);
    }

    /**
     * Add an alert message to the session.
     *
     * @param string      $message
     * @param string|null $icon
     */
    public static function flashAlert(string $message, ?string $icon = null): void
    {
        static::$flashMessages[] = [
            'type'    => 'alert',
            'message' => $message,
            'icon'    => $icon,
        ];

        session()->flash('fastlane-messages', static::$flashMessages);
    }

    /**
     * Add a danger message to the session.
     *
     * @param string      $message
     * @param string|null $icon
     */
    public static function flashDanger(string $message, ?string $icon = null): void
    {
        static::$flashMessages[] = [
            'type'    => 'danger',
            'message' => $message,
            'icon'    => $icon,
        ];

        session()->flash('fastlane-messages', static::$flashMessages);
    }

    /**
     * Get all flash messages in the session.
     *
     * @return array
     */
    public static function getFlashMessages(): array
    {
        return static::$flashMessages;
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
