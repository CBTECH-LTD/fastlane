<?php

namespace CbtechLtd\Fastlane;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \CbtechLtd\Fastlane\Fastlane
 * @method static void createRole(string $name, array $permissions = ['*'])
 * @method static void createPermission(string $name)
 * @method static Collection entryTypes()
 * @method static array getFlashMessages()
 * @method static MenuManager getMenuManager()
 * @method static EntryType getEntryTypeByIdentifier(string $identifier)
 * @method static EntryType getEntryTypeByClass(string $class)
 * @method static array getAccessTokenAbilities()
 */
class FastlaneFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fastlane';
    }
}
