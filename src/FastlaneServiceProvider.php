<?php

namespace CbtechLtd\Fastlane;

use CbtechLtd\Fastlane\Console\Commands\GenerateMigrationFromEntryTypeCommand;
use CbtechLtd\Fastlane\Console\Commands\GeneratePivotTableCommand;
use CbtechLtd\Fastlane\Console\Commands\InstallEntryTypesCommand;
use CbtechLtd\Fastlane\Console\Commands\MakeEntryTypeCommand;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Commands\CreateSystemAdminCommand;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\FileAttachment\Attachment;
use CbtechLtd\Fastlane\Http\Controllers\EntryAttachmentsController;
use CbtechLtd\Fastlane\Http\Controllers\EntryImagesController;
use CbtechLtd\Fastlane\Http\Middleware\Authenticate;
use CbtechLtd\Fastlane\Http\Middleware\ResolveFastlaneRequest;
use CbtechLtd\Fastlane\Http\Middleware\RedirectIfAuthenticated;
use CbtechLtd\Fastlane\Http\Middleware\SetInertiaRootTemplate;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        $this->bootBlueprintMacro();
        $this->bootUrlMacro();
        $this->bootRoutes();

        /**
         * Define a morph map to decouple the database from our internal structure.
         */

        Relation::morphMap([
            'fastlane_attachments' => Attachment::class,
        ]);

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
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/fastlane'),
            ], 'lang');

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
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'fastlane');

        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        // Register translation
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'fastlane');

        // Register views and menu
        $this->registerViews();

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

        // Add Fastlane guards, providers and password recovery to
        // Authentication configuration.
        Config::set('auth.guards.fastlane-cp', [
            'driver'   => 'session',
            'provider' => 'fastlane-users',
        ]);

        Config::set('auth.guards.fastlane-api', [
            'driver'   => 'sanctum',
            'provider' => 'fastlane-users',
        ]);

        Config::set('auth.providers.fastlane-users', [
            'driver' => 'eloquent',
            'model'  => User::class,
        ]);

        Config::set('auth.passwords.fastlane-users', [
            'provider' => 'fastlane-users',
            'table'    => 'fastlane_password_resets',
            'expire'   => 60,
            'throttle' => 60,
        ]);
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
        Inertia::share('app.assets', [
            'logoImage'       => config('fastlane.asset_logo_img'),
            'loginBackground' => config('fastlane.asset_login_bg'),
        ]);

        Inertia::share('auth.user', function () {
            if (Auth::user()) {
                return [
                    'attributes' => Auth::user()->toArray(),
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
                return FastlaneFacade::getMenuManager()->build(app()->make(config('fastlane.menu')));
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
        /** @var Router $router */
        $router = $this->app['router'];

        if (! in_array('fastlane.auth', $router->getMiddleware())) {
            $router->aliasMiddleware('fastlane.auth', Authenticate::class);
        }

        if (! in_array('fastlane.guest', $router->getMiddleware())) {
            $router->aliasMiddleware('fastlane.guest', RedirectIfAuthenticated::class);
        }

        if (! in_array('fastlane.resolve', $router->getMiddleware())) {
            $router->aliasMiddleware('fastlane.resolve', ResolveFastlaneRequest::class);
        }

        // Add a macro to generate Control Panel routes
        Router::macro('fastlaneControlPanel', function (string $prefix, string $controller, array $disabledRoutes = []) {
            $this->group(['prefix' => '/entry-types/' . $prefix], function () use ($prefix, $controller, $disabledRoutes) {
                if (! in_array('index', $disabledRoutes)) {
                    $this->get('/', [$controller, 'index'])->name("{$prefix}.index");
                }

                if (! in_array('create', $disabledRoutes)) {
                    $this->get('/new', [$controller, 'create'])->name("{$prefix}.create");
                }

                if (! in_array('store', $disabledRoutes)) {
                    $this->post('/', [$controller, 'store'])->name("{$prefix}.store");
                }

                if (! in_array('edit', $disabledRoutes)) {
                    $this->get('/{id}', [$controller, 'edit'])->name("{$prefix}.edit");
                }

                if (! in_array('update', $disabledRoutes)) {
                    $this->patch('/{id}', [$controller, 'update'])->name("{$prefix}.update");
                }

                if (! in_array('delete', $disabledRoutes)) {
                    $this->delete('/{id}', [$controller, 'delete'])->name("{$prefix}.delete");
                }

                // Attachment management routes
                if (! in_array('attachments', $disabledRoutes)) {
                    $this->post('/attachments/{fieldName}', [EntryAttachmentsController::class, 'store'])->name("{$prefix}.attachments");
                    $this->delete('/attachments/{fieldName}', [EntryAttachmentsController::class, 'delete']);
                }

                // Image upload routes
                if (! in_array('image', $disabledRoutes)) {
                    $this->post('/images/{fieldName}', [EntryImagesController::class, 'store'])->name("{$prefix}.images");
                }
            });
        });

        // Add a macro to generate Content API routes
        Router::macro('fastlaneContentApi', function (string $prefix, string $controller) {
            $this->group(['prefix' => '/entry-types/' . $prefix,], function () use ($prefix, $controller) {
                $this->get('/entries', [$controller, 'collection'])->name("{$prefix}.collection");
                $this->get('/entries/{id}', [$controller, 'single'])->name("{$prefix}.single");
            });
        });

        // Set up routes.
        $this->setupControlPanelRoutes();
        $this->setupContentAPIRoutes();
    }

    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'fastlane');

        Inertia::version(function () {
            return md5_file(__DIR__ . '/../public/mix-manifest.json');
        });
    }

    protected function registerDisks(): void
    {
        $this->app->config['filesystems.disks.fastlane'] = [
            'driver' => 'local',
            'root'   => storage_path('app/fastlane'),
        ];
    }

    protected function setupControlPanelRoutes(): void
    {
        $middleware = array_merge(
            config('fastlane.control_panel.middleware'), ['inertia:fastlane']);

        Route::middleware($middleware)
            ->prefix(config('fastlane.control_panel.url_prefix'))
            // TODO: Change it to fastlane.cp for better CMS isolation...
            ->as('cp.')
            ->group(__DIR__ . '/../routes/cp.php');
    }

    protected function setupContentAPIRoutes(): void
    {
        $middleware = array_merge(config('fastlane.api.middleware'), []);

        Route::middleware($middleware)
            ->prefix(config('fastlane.api.url_prefix'))
            ->as('fastlane.api.')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
