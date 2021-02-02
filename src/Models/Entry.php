<?php

namespace CbtechLtd\Fastlane\Models;

use Altek\Accountant\Contracts\Recordable;
use Altek\Accountant\Recordable as RecordableTrait;
use Altek\Eventually\Eventually;
use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\Fields\Types\FieldCollection;
use CbtechLtd\Fastlane\Fields\Types\Relationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Entry extends Model implements Recordable
{
    use RecordableTrait, Eventually, Activable, HasUuid, HasDynamicRelations;

    /** @var EntryType | string  */
    protected string $entryType;

    protected $table = '';

    protected $fillable = [];

    protected $casts = [];

    /**
     * Get the model table.
     *
     * @return string
     * @throws \Exception
     */
    public function getTable()
    {
        if ($this->table === '') {
            throw new \Exception('No table set in the model.');
        }

        return $this->table;
    }

    /**
     * Get the entry type defined to the model.
     *
     * @return string
     */
    public function getEntryType(): string
    {
        return $this->entryType;
    }

    /**
     * Set the entry type used by the model.
     *
     * @param string $entryType
     * @return $this
     */
    public function setEntryType(string $entryType): self
    {
        $this->entryType = $entryType;
        $this->loadAttributesFromEntryType();

        if (!is_null($this->table)) {
            $this->setTable($entryType::table());
        }

        return $this;
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Load fillable and casts from the entry type.
     */
    protected function loadAttributesFromEntryType(): void
    {
        $fields = FieldCollection::make($this->getEntryType()::fields());

        // Get the attributes and its casts and merge into the model.
        $casts = $fields->getAttributes()->mapWithKeys(fn (Field $field) => [
            $field->getAttribute() => $field->castUsing()
        ]);

        $this->mergeCasts($casts->all());
        $this->mergeFillable($casts->keys()->all());

        // Add the dynamic relationships to the model.
        $fields->getRelationships()->each(function (Relationship $field) {
            if (! method_exists(static::class, $field->getRelationshipMethod())) {
                $this->addDynamicRelation(
                    $field->getRelationshipMethod(),
                    $field->getRelationshipResolver()
                );
            }
        });
    }
}
