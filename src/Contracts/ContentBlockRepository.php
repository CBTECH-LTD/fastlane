<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Contracts;

interface ContentBlockRepository
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
     * @return array<ContentBlock>
     */
    public function all(): array;

    /**
     * Find an entry type by its key.
     *
     * @param string $key
     * @return ContentBlock
     */
    public function findByKey(string $key): ContentBlock;

    /**
     * Find an entry type by its class.
     *
     * @param string $class
     * @return ContentBlock
     */
    public function findByClass(string $class): ContentBlock;
}
