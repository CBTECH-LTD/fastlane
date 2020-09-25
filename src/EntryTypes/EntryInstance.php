<?php

namespace CbtechLtd\Fastlane\EntryTypes;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Contracts\EntrySchema as EntrySchemaContract;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ReadValue;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WriteValue;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldValue;
use CbtechLtd\Fastlane\Support\Schema\Fields\RelationField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EntryInstance implements EntryInstanceContract
{
    protected EntryTypeContract $type;
    protected EntrySchemaContract $schema;
    protected Model $model;

    public function __construct(EntryTypeContract $entryType, Model $model)
    {
        $this->type = $entryType;
        $this->model = $model;

        $this->resolve();
    }

    public function id()
    {
        return $this->model->getKey();
    }

    public function title(): string
    {
        if (method_exists($this->model, 'toString')) {
            return $this->model->toString();
        }

        /** @var ReadValue $field */
        $field = Collection::make($this->schema()->getFields())
            ->first(fn(SchemaField $f) => $f instanceof ReadValue);

        $value = ! is_null($field)
            ? $field->readValue($this)->value()
            : null;

        return $value ?? '';
    }

    public function type(): EntryTypeContract
    {
        return $this->type;
    }

    public function get(string $field, $default = null): FieldValue
    {
        $field = $this->schema()->findField($field);

        if ($field instanceof ReadValue) {
            return $field->readValue($this);
        }

        return new FieldValue($field, null);
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function schema(): EntrySchemaContract
    {
        return $this->schema;
    }

    public function saveModel(): self
    {
        if ($this->model()->isDirty()) {
            $this->model()->save();
            $this->model()->refresh();
        }

        return $this;
    }

    public function toArray()
    {
        return Collection::make($this->schema->getFields())
            ->filter(fn(SchemaField $field) => $field instanceof ReadValue)
            ->mapWithKeys(function (SchemaField $field) {
                return $this->get($field->getName())->toArray();
            })->toArray();
    }

    protected function resolve(): void
    {
        $this->schema = new EntrySchema($this);

        $this->addRelationshipsToModel();
        $this->addAttributesToModel();
    }

    protected function addRelationshipsToModel(): void
    {
        Collection::make($this->schema()->getFields())
            ->filter(fn(SchemaField $field) => $field instanceof RelationField)
            ->each(function (RelationField $ft) {
                // We dynamically add a relation to the model if there's no
                // method declared with the same name.
                if (! method_exists($this, $ft->getRelationshipName())) {
                    $this->model::resolveRelationUsing(
                        $ft->getRelationshipName(),
                        $ft->getRelationshipMethod()
                    );
                }
            });
    }

    protected function addAttributesToModel(): void
    {
        $fields = Collection::make($this->schema()->getFields())
            ->filter(fn(SchemaField $field) => $field instanceof WriteValue)
            ->mapWithKeys(function (WriteValue $ft) {
                return $ft->toModelAttribute();
            });

        $this->model()->mergeFillable($fields->keys()->all());
        $this->model()->mergeCasts($fields->filter()->all());
    }
}
