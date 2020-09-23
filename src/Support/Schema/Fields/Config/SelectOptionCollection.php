<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Config;

use Illuminate\Database\Eloquent\Collection;

class SelectOptionCollection extends Collection
{
    protected ?\Closure $lazyResolver = null;
    protected bool $lazyLoaded = false;

    public static function newLazy(\Closure $callback): SelectOptionCollection
    {
        return tap(new static(), function (SelectOptionCollection $collection) use ($callback) {
            $collection->lazyResolver = $callback;
        });
    }

    public function resolveLazyLoad(array $payload = []): self
    {
        if ($this->lazyResolver && ! $this->lazyLoaded) {
            $this->items = call_user_func_array($this->lazyResolver, $payload);
            $this->lazyLoaded = true;
        }

        return $this;
    }

    public function toArray()
    {
        $this->resolveLazyLoad();

        return parent::toArray();
    }
}
