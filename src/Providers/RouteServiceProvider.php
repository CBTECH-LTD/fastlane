<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\EntryTypes\EntryTypeRouteCollection;
use CbtechLtd\Fastlane\Http\Middleware\Authenticate;
use CbtechLtd\Fastlane\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /** @var Router $router */
        $router = $this->app['router'];

        if (! in_array('fastlane.auth', $router->getMiddleware())) {
            $router->aliasMiddleware('fastlane.auth', Authenticate::class);
        }

        if (! in_array('fastlane.guest', $router->getMiddleware())) {
            $router->aliasMiddleware('fastlane.guest', RedirectIfAuthenticated::class);
        }

        // Add a macro to generate Control Panel routes
        Router::macro('fastlaneControlPanel', function (string $prefix, EntryTypeRouteCollection $routes) {
            $routes->register($this);
        });

        // Add a macro to generate relative paths easily.
        UrlGenerator::macro('relative', function (string $routeName, $routeParams = null) {
            return route($routeName, $routeParams, false);
        });

        // Set up routes.
        $this->setupControlPanelRoutes();
        $this->setupContentAPIRoutes();
    }

    protected function setupControlPanelRoutes(): void
    {
        $middleware = config('fastlane.control_panel.middleware');

        Route::middleware($middleware)
            ->prefix(config('fastlane.control_panel.url_prefix'))
            ->as('fastlane.cp.')
            ->group(__DIR__ . '/../../routes/cp.php');
    }

    protected function setupContentAPIRoutes(): void
    {
        $middleware = array_merge(config('fastlane.api.middleware'), []);

        Route::middleware($middleware)
            ->prefix(config('fastlane.api.url_prefix'))
            ->as('fastlane.api.')
            ->group(__DIR__ . '/../../routes/api.php');
    }
}
