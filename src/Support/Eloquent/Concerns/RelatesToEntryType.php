<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\Fastlane\Support\Schema\Fields\RelationField;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use ReflectionClass;

trait RelatesToEntryType
{
    protected bool $attributesFromSchema = true;
    protected bool $relationsFromSchema = true;

    protected function getEntryType(): EntryType
    {
        $name = (new ReflectionClass($this))->getName() . 'EntryType';

        if (! class_exists($name)) {
            ClassDoesNotExistException::entryType($name);
        }

        return app()->make($name);
    }

    public function getEntryTypeRelations(): array
    {
        return $this->entryTypeRelations;
    }

    public function initializeRelatesToEntryType(): void
    {
        if (! $this->attributesFromSchema && ! $this->relationsFromSchema) {
            return;
        }

        $allFields = Collection::make($this->getEntryType()->fields())
            ->map(
                function (SchemaField $field) {
                    if ($field instanceof FieldPanel) {
                        return $field->getFields();
                    }

                    return [$field];
                }
            )
            ->flatten(0)
            ->mapToGroups(function (SchemaField $field) {
                $group = $field instanceof RelationField
                    ? 'relations'
                    : 'attributes';

                return [$group => $field];
            });

        if ($this->attributesFromSchema && $allFields->has('attributes')) {
            $this->loadAttributesFromEntrySchema($allFields->get('attributes'));
        }

        if ($this->relationsFromSchema && $allFields->has('relations')) {
            $this->loadRelationsFromEntrySchema($allFields->get('relations'));
        }
    }

    /**
     * @param Collection<SchemaField> $fields
     */
    private function loadAttributesFromEntrySchema(Collection $fields): void
    {
        $fields = $fields->mapWithKeys(function (SchemaField $ft) {
            if ($ft->isShownOnCreate() || $ft->isShownOnUpdate()) {
                return $ft->toModelAttribute();
            }

            return [];
        });

        $this->mergeFillable($fields->except($this->fillable)->keys()->all());
        $this->mergeCasts($fields->except(array_keys($this->casts))->filter()->all());
    }

    /**
     * @param Collection<SchemaField> $fields
     */
    private function loadRelationsFromEntrySchema(Collection $fields): void
    {
        $fields->each(function (RelationField $ft) {
            // We dynamically add a relation to the model if there's no
            // method declared with the same name.
            if (! method_exists($this, $ft->getRelationshipName())) {
                static::resolveRelationUsing(
                    $ft->getRelationshipName(),
                    $ft->getRelationshipMethod()
                );
            }
        });
    }
}
