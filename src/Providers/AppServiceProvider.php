<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\Console\Commands\GenerateMigrationFromEntryTypeCommand;
use CbtechLtd\Fastlane\Console\Commands\GeneratePivotTableCommand;
use CbtechLtd\Fastlane\Console\Commands\InstallEntryTypesCommand;
use CbtechLtd\Fastlane\Console\Commands\MakeEntryTypeCommand;
use CbtechLtd\Fastlane\ContentBlocks\ContentBlockRepository;
use CbtechLtd\Fastlane\Contracts\ContentBlockRepository as ContentBlockRepositoryContract;
use CbtechLtd\Fastlane\Contracts\EntryTypeRepository as EntryTypeRepositoryContract;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Commands\CreateSystemAdminCommand;
use CbtechLtd\Fastlane\EntryTypes\EntryTypeRepository;
use CbtechLtd\Fastlane\Http\Middleware\SetInertiaRootTemplate;
use CbtechLtd\Fastlane\Support\Menu\MenuManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->bootInertia();
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
            EntryTypeRepositoryContract::class    => EntryTypeRepository::class,
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

    protected function bootInertia(): void
    {
        // Register the route middleware that sets the root path
        // so we can use Inertia on backend and frontend.
        $this->app['router']->aliasMiddleware('inertia', SetInertiaRootTemplate::class);

        // Set data that must be available to all components.
        Inertia::share('app.name', Config::get('app.name'));

        Inertia::share('app.baseUrl', Config::get('app.url'));

        Inertia::share('app.requestUrl', request()->path());

//        Inertia::share('app.cpUrls', function () {
//            return [
//                'fileManager' => route('cp.file-manager.index'),
//            ];
//        });

        Inertia::share('app.csrfToken', function () {
            return csrf_token();
        });

        Inertia::share('app.assets', [
            'logoImage'       => config('fastlane.asset_logo_img'),
            'loginBackground' => config('fastlane.asset_login_bg'),
        ]);

        Inertia::share('auth.user', function () {
            if (Auth::user()) {
                return [
                    'attributes' => Auth::user()->toShallowArray(),
                ];
            }

            return null;
        });

        Inertia::share('errors', function () {
            if (Session::get('errors')) {
                $bags = [];

                foreach (Session::get('errors')->getBags() as $bag => $error) {
                    $bags[$bag] = $error->getMessages();
                }

                return (object)$bags;
            }

            return (object)[];
        });

        Inertia::share('flashMessages', function () {
            return Session::get('fastlane-messages');
        });

        Inertia::share('menu', function () {
            if (Auth::check()) {
                return (new MenuManager)->build(app()->make(config('fastlane.menu')));
            }

            return null;
        });
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'fastlane');

        Inertia::version(function () {
            return md5_file(__DIR__ . '/../../public/mix-manifest.json');
        });
    }
}
