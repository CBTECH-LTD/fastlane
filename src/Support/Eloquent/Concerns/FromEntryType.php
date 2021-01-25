<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Fields\Types\Relationship;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;

trait FromEntryType
{
    static array $entryTypes = [];

    /**
     * @return string|EntryType
     */
    public static function getEntryType()
    {
        if (property_exists(static::class, 'entryType')) {
            return static::$entryType;
        }

        return '';
    }

    public static function bootFromEntryType(): void
    {
        // Get all fields of the entry type and add the relationships.
        $fields = FieldCollection::make(static::getEntryType()::fields())->getRelationships();

        $fields->each(function (Relationship $field) {
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
        $fields = FieldCollection::make(static::getEntryType()::fields())->getAttributes();

        $casts = $fields->mapWithKeys(function (Field $field) {
            return [
                $field->getAttribute() => $field->castUsing(),
            ];
        });

        $this->mergeCasts($casts->all());
        $this->mergeFillable($casts->keys()->all());
    }
}
