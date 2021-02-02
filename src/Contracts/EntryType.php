<?php

namespace CbtechLtd\Fastlane\Contracts;

use CbtechLtd\Fastlane\EntryTypes\EntryTypeRouteCollection;
use CbtechLtd\Fastlane\Models\Entry;
use CbtechLtd\Fastlane\Repositories\EntryRepository;

interface EntryType
{
    /**
     * Setup the entry type.
     *
     * @throws \ReflectionException
     */
    public static function boot(): void;

    /**
     * Determine a unique identifier.
     *
     * @return string
     */
    public static function key(): string;

    /**
     * The singular and plural name of the entry type.
     *
     * @return array
     */
    public static function label(): array;

    /**
     * The icon to be used or empty for no icon.
     *
     * @return string|null
     */
    public static function icon(): ?string;

    /**
     * The database table that holds the entry type entities.
     *
     * @return string
     */
    public static function table(): string;

    /**
     * The repository that should be used to query the entry type.
     *
     * @return EntryRepository
     */
    public static function repository(): EntryRepository;

    /**
     * Install the Entry Type.
     */
    public static function install(): void;

    /**
     * The list of fields of the entry type.
     *
     * @return array
     */
    public static function fields(): array;

    /**
     * Generate a string to be used as the description
     * of the given model.
     *
     * @param Entry $model
     * @return string
     */
    public static function entryTitle(Entry $model): string;

    /**
     * Determine the default column to be used to order the results
     * in the listing page.
     *
     * @return string
     */
    public static function listingDefaultOrder(): string;
}
