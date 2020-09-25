<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes;

use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EntryTypeRouteCollection
{
    /** @var array<EntryTypeRoute> */
    private array $routes;

    /** @var string */
    private string $prefix;

    public static function make(string $prefix, array $routes = []): EntryTypeRouteCollection
    {
        return new static($prefix, $routes);
    }

    public function __construct(string $prefix, array $routes = [])
    {
        $this->prefix = $prefix;
        $this->routes = Collection::make($routes)->mapWithKeys(function (EntryTypeRoute $route) {
            return [$route->name() => $route];
        })->all();
    }

    public function has(string $name): bool
    {
        return Arr::has($this->routes, $name);
    }

    public function get(string $name): ?EntryTypeRoute
    {
        return Arr::get($this->routes, $name);
    }

    public function all(): array
    {
        return $this->routes;
    }

    public function register(Router $router): void
    {
        $router->group(['prefix' => '/entry-types/' . $this->prefix], function () use ($router) {
            foreach ($this->routes as $route) {
                $route->register($router);
            }
        });
    }
}
