<?php

declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Exceptions\ClassDoesNotExistException;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Support\Collection;
use ReflectionClass;

trait RelatesToEntryType
{
    protected bool $fillableFromSchema = true;

    protected function getEntryType(): EntryType
    {
        $name = (new ReflectionClass($this))->getName() . 'EntryType';

        if (! class_exists($name)) {
            ClassDoesNotExistException::entryType($name);
        }

        return app()->make($name);
    }

    public function initializeRelatesToEntryType(): void
    {
        if ($this->fillableFromSchema) {
            $schema = $this->getEntryType()->schema();

            $fields = Collection::make($schema->getDefinition()->getFields())->mapWithKeys(
                function (SchemaFieldType $ft) {
                    if ($ft->isShownOnCreate() || $ft->isShownOnUpdate()) {
                        return $ft->toModelAttribute();
                    }

                    return [];
                }
            );

            $this->mergeFillable($fields->except($this->fillable)->keys()->all());
            $this->mergeCasts($fields->except(array_keys($this->casts))->filter()->all());
        }
    }
}
