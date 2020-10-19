<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\ContentBlocks;

use CbtechLtd\Fastlane\Contracts\ContentBlock as ContentBlockContract;
use CbtechLtd\Fastlane\Contracts\ContentBlockRepository as ContentBlockRepositoryContract;
use CbtechLtd\Fastlane\Exceptions\ContentBlockNotRegisteredException;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class ContentBlockRepository implements ContentBlockRepositoryContract
{
    protected Collection $items;

    public function __construct()
    {
        $this->items = new Collection;
    }

    /**
     * Register an entry type class in the repository.
     *
     * @param string $class
     */
    public function register(string $class): void
    {
        Assert::isAOf($class, \CbtechLtd\Fastlane\Contracts\ContentBlock::class);

        $this->items->push($class);
    }

    /**
     * Get all entry types.
     *
     * @return array<ContentBlockRepositoryContract>
     */
    public function all(): array
    {
        return $this->items->all();
    }

    /**
     * Find an entry type by its key.
     *
     * @param string $key
     * @return ContentBlockContract
     * @throws ContentBlockNotRegisteredException
     */
    public function findByKey(string $key): string
    {
        if (! $item = $this->items->first(fn(string $class) => $class::key() === $key)) {
            throw ContentBlockNotRegisteredException::keyNotRegistered($key);
        }

        return $item;
    }

    /**
     * Find an entry type by its class.
     *
     * @param string $class
     * @return ContentBlockContract
     * @throws ContentBlockNotRegisteredException
     */
    public function findByClass(string $class): string
    {
        if (! $item = $this->items->first(fn(string $c) => $c === $class)) {
            throw ContentBlockNotRegisteredException::classNotRegistered($class);
        }

        return $item;
    }
}
