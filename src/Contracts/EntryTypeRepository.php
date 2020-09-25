<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Contracts;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;

interface EntryTypeRepository
{
    /**
     * Register an entry type class in the repository.
     *
     * @param string $class
     */
    public function register(string $class): void;

    /**
     * Get all entry types.
     *
     * @return array<EntryTypeContract>
     */
    public function all(): array;

    /**
     * Find an entry type by its key.
     *
     * @param string $key
     * @return EntryTypeContract
     */
    public function findByKey(string $key): string;

    /**
     * Find an entry type by its class.
     *
     * @param string $class
     * @return EntryTypeContract
     */
    public function findByClass(string $class): string;
}
