<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Fastlane;
use Illuminate\Routing\Router;

class EntryTypeRoute
{
    /** @var string|EntryTypeContract */
    private string $entryType;

    private string $path;
    private string $name;
    private string $controller;
    private string $handler;
    private string $method = 'get';

    public static function get(string $entryType, string $path, string $name): EntryTypeRoute
    {
        return new static($entryType, $path, $name);
    }

    public static function post(string $entryType, string $path, string $name): EntryTypeRoute
    {
        return (new static($entryType, $path, $name))->asPost();
    }

    public static function put(string $entryType, string $path, string $name): EntryTypeRoute
    {
        return (new static($entryType, $path, $name))->asPut();
    }

    public static function patch(string $entryType, string $path, string $name): EntryTypeRoute
    {
        return (new static($entryType, $path, $name))->asPatch();
    }

    public static function delete(string $entryType, string $path, string $name): EntryTypeRoute
    {
        return (new static($entryType, $path, $name))->asDelete();
    }

    public function __construct(string $entryType, string $path, string $name)
    {
        $this->entryType = $entryType;
        $this->path = $path;
        $this->name = $name;
        $this->handler = $name;
        $this->controller = $entryType::controller();
    }

    public function uses(string $controller, string $handler): self
    {
        $this->controller = $controller;
        $this->handler = $handler;
        return $this;
    }

    public function asGet(): self
    {
        $this->method = 'get';
        return $this;
    }

    public function asPost(): self
    {
        $this->method = 'post';
        return $this;
    }

    public function asPut(): self
    {
        $this->method = 'put';
        return $this;
    }

    public function asPatch(): self
    {
        $this->method = 'patch';
        return $this;
    }

    public function asDelete(): self
    {
        $this->method = 'delete';
        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function routeName(bool $full = true): string
    {
        $name = "{$this->entryType::key()}.{$this::name()}";

        if (! $full) {
            return $name;
        }

        return Fastlane::makeCpRouteName($name);
    }

    public function register(Router $router): void
    {
        $router->{$this->method}($this->path)
            ->uses($this->controller . '@' . $this->handler)
            ->name($this->routeName(false));
    }

    public function url($params = null, bool $absolute = true): string
    {
        return route($this->routeName(), $params, $absolute);
    }
}
