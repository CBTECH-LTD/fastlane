<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Facades;

use CbtechLtd\Fastlane\Contracts\ContentBlockRepository;
use Illuminate\Support\Facades\Facade;

/**
 * Class EntryType
 *
 * @method static void register(string $class)
 *
 * @see     ContentBlockRepository
 * @package CbtechLtd\Fastlane\Facades
 */
class ContentBlock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ContentBlockRepository::class;
    }
}
