<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Facades\EntryType;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class EntryTypesServiceProvider extends ServiceProvider
{
    /**
     * @var array|string[]
     */
    protected array $builtInTypes = [
        // ContentEntryType::class,
        // FileManagerEntryType::class,
        BackendUserEntryType::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Merge the user-defined types with the built-in types.
        // We don't add built-in types to the default config file because
        // it would error prone (devs could just remove it), but for now
        // we require all these built-in types to be correctly registered.
        $classes = array_merge(config('fastlane.entry_types'), $this->builtInTypes);

        // Iterate over every entry type and register it in the
        // entry type repository.
        Collection::make($classes)->each(function ($typeClass) {
            EntryType::register($typeClass);
        });
    }
}
