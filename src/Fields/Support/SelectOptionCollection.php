<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Support;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class SelectOptionCollection implements Arrayable
{
    private array $items;
    private ?Closure $lazyLoader = null;
    private bool $lazyLoaded = false;

    public static function make(array $items = []): SelectOptionCollection
    {
        return new static($items);
    }

    public static function lazy(callable $callback): SelectOptionCollection
    {
        return tap(static::make(), function (SelectOptionCollection $collection) use ($callback) {
            $collection->lazyLoader = $callback;
        });
    }

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function collection(): Collection
    {
        return Collection::make($this->items)->values();
    }

    public function all(): array
    {
        return $this->items;
    }

    public function select(array $values): self
    {
        foreach ($this->items as $item) {
            $item->select(in_array($item->getValue(), $values));
        }

        return $this;
    }

    public function selected(): Collection
    {
        return $this->collection()->filter(
            fn(SelectOption $option) => $option->isSelected()
        );
    }

    public function load(array $payload = []): self
    {
        if (! is_callable($this->lazyLoader) || $this->lazyLoaded) {
            return $this;
        }

        $this->items = call_user_func_array($this->lazyLoader, $payload);

        return $this;
    }

    public function toArray()
    {
        return Collection::make($this->load()->items)->toArray();
    }
}