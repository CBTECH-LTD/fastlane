<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Facades;

use CbtechLtd\Fastlane\Contracts\EntryTypeRepository;
use Illuminate\Support\Facades\Facade;

/**
 * Class EntryType
 *
 * @method static void register(string $class)
 * @method static array all()
 * @method static string findByKey(string $key)
 * @method static string findByClass(string $class)
 *
 * @see     EntryTypeRepository
 * @package CbtechLtd\Fastlane\Facades
 */
class EntryType extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EntryTypeRepository::class;
    }
}
