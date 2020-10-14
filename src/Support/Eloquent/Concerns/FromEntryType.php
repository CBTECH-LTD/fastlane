<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\FieldCast;
use CbtechLtd\Fastlane\Fields\RelationshipCast;
use CbtechLtd\Fastlane\Fields\Types\Relationship;
use Illuminate\Support\Collection;

trait FromEntryType
{
    /** @var array<EntryType> */
    protected static array $entryTypes = [];

    /** @var EntryType */
    protected EntryType $entryType;

    /**
     * @param string | EntryType $class
     */
    public static function withEntryType(string $class): void
    {
        static::$entryTypes[static::class] = $class;
    }

    /**
     * @param EntryType $entryType
     */
    public function setEntryType(EntryType $entryType): void
    {
        $this->entryType = $entryType;
    }

    /**
     * @return EntryType
     */
    public function getEntryType()
    {
        return $this->entryType;
    }

    public static function bootFromEntryType(): void
    {
        if (! $entryType = static::$entryTypes[static::class] ?? null) {
            return;
        }

        // Get all fields of the entry type and add the relationships.
        $fields = Collection::make($entryType::newInstance()->getFields()->flattenFields());

        $fields
            ->filter(fn(Field $field) => $field instanceof Relationship)
            ->each(function (Relationship $field) {
                // We dynamically add a relation to the model if there's no
                // method declared with the same name.
                if (! method_exists(static::class, $field->getRelationshipMethod())) {
                    static::resolveRelationUsing(
                        $field->getRelationshipMethod(),
                        $field->getRelationshipResolver()
                    );
                }
            });
    }

    public function initializeFromEntryType(): void
    {
        if (! $entryType = static::$entryTypes[static::class] ?? null) {
            return;
        }

        $this->setEntryType($entryType::newInstance($this));

        // Get all fields of the entry type and add attributes
        // and its casts to the model.
        $fields = Collection::make($this->entryType->getFields()->flattenFields());

        $casts = $fields
            ->mapWithKeys(function (Field $field) {
                if ($field instanceof Relationship) {
                    return [
                        "{$field->getAttribute()}__relation" => RelationshipCast::class,
                    ];
                }

                return [
                    $field->getAttribute() => FieldCast::class,
                ];
            });

        $this->mergeCasts($casts->all());
        $this->mergeFillable($casts->keys()->all());
    }
}
