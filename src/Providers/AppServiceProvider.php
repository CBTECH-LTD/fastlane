<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\Console\Commands\GenerateMigrationFromEntryTypeCommand;
use CbtechLtd\Fastlane\Console\Commands\GeneratePivotTableCommand;
use CbtechLtd\Fastlane\Console\Commands\InstallEntryTypesCommand;
use CbtechLtd\Fastlane\Console\Commands\MakeEntryTypeCommand;
use CbtechLtd\Fastlane\ContentBlocks\ContentBlockRepository;
use CbtechLtd\Fastlane\Contracts\ContentBlockRepository as ContentBlockRepositoryContract;
use CbtechLtd\Fastlane\Contracts\EntryTypeRegistrar as EntryTypeRepositoryContract;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Commands\CreateSystemAdminCommand;
use CbtechLtd\Fastlane\EntryTypes\EntryTypeRegistrar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->bootBlueprintMacro();

        /*
         * Optional methods to load your package assets
         */

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/config.php' => config_path('fastlane.php'),
            ], 'fastlane-config');

            $this->publishes([
                __DIR__ . '/../../public' => public_path('vendor/fastlane'),
            ], 'fastlane-assets');

            $this->publishes([
                __DIR__ . '/../../resources/lang' => resource_path('lang/vendor/fastlane'),
            ], 'fastlane-lang');

            // Registering package commands.
            $this->commands([
                MakeEntryTypeCommand::class,
                InstallEntryTypesCommand::class,
                GenerateMigrationFromEntryTypeCommand::class,
                GeneratePivotTableCommand::class,
                CreateSystemAdminCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'fastlane');
        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'fastlane');
        $this->registerViews();

        $this->registerSingletons();
    }

    protected function registerSingletons(): void
    {
        collect([
            EntryTypeRepositoryContract::class    => EntryTypeRegistrar::class,
            ContentBlockRepositoryContract::class => ContentBlockRepository::class,
        ])->each(function ($concrete, $abstract) {
            $this->app->singleton($abstract, $concrete);

            if (property_exists($concrete, 'bindings')) {
                foreach ($concrete::$bindings as $bAbs => $bConcrete) {
                    $this->app->bind($bAbs, $bConcrete);
                }
            }
        });
    }

    protected function bootBlueprintMacro(): void
    {
        Blueprint::macro('cmsCommon', function () {
            $this->id('id');
            $this->string('hashid', 30)->nullable();
            $this->timestamps();
        });

        Blueprint::macro('activable', function ($default = true) {
            $this->boolean('is_active')->default($default);
        });

        Blueprint::macro('geoCoordinates', function () {
            $this->decimal('latitude', 10, 8)->nullable();
            $this->decimal('longitude', 11, 8)->nullable();
        });
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'fastlane');
    }
}
