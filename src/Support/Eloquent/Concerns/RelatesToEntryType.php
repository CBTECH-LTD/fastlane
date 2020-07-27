<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\RelationField;
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
        if ($this->attributesFromSchema) {
            $this->loadAttributesFromEntrySchema(
                $this->getEntryType()->schema()->all()
            );
        }

        if ($this->relationsFromSchema) {
            $this->loadRelationsFromEntrySchema(
                $this->getEntryType()->schema()->all()
            );
        }
    }

    /**
     * @param SchemaField[] $fields
     */
    private function loadAttributesFromEntrySchema(array $fields): void
    {
        $fields = Collection::make($fields)
            ->mapWithKeys(function (SchemaField $ft) {
                if ($ft->isShownOnCreate() || $ft->isShownOnUpdate()) {
                    return $ft->toModelAttribute();
                }

                return [];
            });

        $this->mergeFillable($fields->except($this->fillable)->keys()->all());
        $this->mergeCasts($fields->except(array_keys($this->casts))->filter()->all());
    }

    /**
     * @param SchemaField[] $fields
     */
    private function loadRelationsFromEntrySchema(array $fields): void
    {
        Collection::make($fields)
            ->filter(fn(SchemaField $ft) => $ft instanceof RelationField)
            ->each(function (RelationField $ft) {
                static::resolveRelationUsing(
                    $ft->getRelationshipName(),
                    $ft->getRelationshipMethod()
                );
            });
    }
}
