<?php

namespace CbtechLtd\Fastlane;

use CbtechLtd\Fastlane\Console\Commands\InstallContentTypesCommand;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserResource;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Commands\CreateSystemAdminCommand;
use CbtechLtd\Fastlane\Http\Middleware\SetInertiaRootTemplate;
use CbtechLtd\Fastlane\Support\Menu\Contracts\MenuManager as MenuManagerContract;
use CbtechLtd\Fastlane\Support\Menu\MenuManager;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class FastlaneServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->bootAccessControl();
        $this->bootInertia();
        $this->bootUrlMacro();
        $this->bootBlueprintMacro();
        $this->bootRoutes();

        /*
         * Optional methods to load your package assets
         */

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('fastlane.php'),
            ], 'fastlane-config');

            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/fastlane'),
            ], 'fastlane-assets');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/fastlane'),
            ], 'views');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/fastlane'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands([
                InstallContentTypesCommand::class,
                CreateSystemAdminCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'fastlane');

        // Register views and menu
        $this->registerViews();
        $this->registerMenu();

        // Register the main class to use with the facade
        $this->app->singleton('fastlane', function () {
            return new Fastlane;
        });
    }

    protected function bootAccessControl(): void
    {
        // Implicitly grant "Super Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if (config('fastlane.super_admin') && $user->hasRole(BackendUserEntryType::ROLE_SYSTEM_ADMIN)) {
                return true;
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

        Inertia::share('auth.user', function () {
            if (Auth::user()) {
                return BackendUserResource::single(Auth::user());
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

        Inertia::share('flashMessage', function () {
            return Session::get('message');
        });

        Inertia::share('menu', function () {
            if (Auth::check()) {
                return app(MenuManager::class)->build();
            }

            return null;
        });
    }

    protected function bootUrlMacro(): void
    {
        UrlGenerator::macro('relative', function (string $routeName, $routeParams = null) {
            return $this->route($routeName, $routeParams, false);
        });
    }

    protected function bootRoutes(): void
    {
        Router::macro('fastlane', function (string $prefix, string $controller) {
            $this->group([
                'prefix' => $prefix,
            ], function () use ($prefix, $controller) {
                $this->get('/', [$controller, 'index'])->name("{$prefix}.index");
                $this->get('/new', [$controller, 'create'])->name("{$prefix}.create");
                $this->post('/', [$controller, 'store'])->name("{$prefix}.store");
                $this->get('/{id}', [$controller, 'edit'])->name("{$prefix}.edit");
                $this->patch('/{id}', [$controller, 'update'])->name("{$prefix}.update");
                $this->delete('/{id}', [$controller, 'delete'])->name("{$prefix}.delete");
            });
        });

        // Set up the Control Panel routes.
        $middleware = array_merge(config('fastlane.control_panel_middleware'), ['inertia:fastlane']);

        Route::middleware($middleware)
            ->prefix('cp')
            ->as('cp.')
            ->group(__DIR__ . '/../routes/cp.php');
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'fastlane');

        Inertia::version(function () {
            return md5_file(__DIR__ . '/../public/mix-manifest.json');
        });
    }

    protected function registerMenu(): void
    {
        $this->app->singleton(MenuManagerContract::class, function () {
            return new MenuManager;
        });
    }
}
