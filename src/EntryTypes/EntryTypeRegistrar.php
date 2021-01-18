<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Contracts\EntryTypeRegistrar as Contract;
use CbtechLtd\Fastlane\Exceptions\EntryTypeNotRegisteredException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class EntryTypeRegistrar implements Contract
{
    /** @var array|string[] */
    public static array $bindings = [];

    /** @var Collection */
    protected Collection $items;

    /** @var Application */
    private Application $app;

    /**
     * EntryTypeRepository constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->items = new Collection;
        $this->app = $app;
    }

    /**
     * Register an entry type class in the repository.
     *
     * @param string|EntryTypeContract $class
     */
    public function register(string $class): void
    {
        Assert::isAOf($class, EntryTypeContract::class);

        $class::boot();

        $this->items->push($class);
    }

    /**
     * Get all entry types.
     *
     * @return array<EntryTypeContract>
     */
    public function all(): array
    {
        return $this->items->all();
    }

    /**
     * Find an entry type by its key.
     *
     * @param string $key
     * @return EntryTypeContract
     * @throws EntryTypeNotRegisteredException
     */
    public function findByKey(string $key): string
    {
        if (! $item = $this->items->first(fn (string $class) => $class::key() === $key)) {
            throw EntryTypeNotRegisteredException::keyNotRegistered($key);
        }

        return $item;
    }

    /**
     * Find an entry type by its class.
     *
     * @param string $class
     * @return EntryTypeContract
     * @throws EntryTypeNotRegisteredException
     */
    public function findByClass(string $class): string
    {
        if (! $item = $this->items->first(fn (string $c) => $c === $class)) {
            throw EntryTypeNotRegisteredException::classNotRegistered($class);
        }

        return $item;
    }
}
