<?php

namespace CbtechLtd\Fastlane;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \CbtechLtd\Fastlane\Fastlane
 * @method static void createRole(string $name, array $permissions = ['*'])
 * @method static void createPermission(string $name)
 * @method static Collection entryTypes()
 * @method static array getFlashMessages()
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
