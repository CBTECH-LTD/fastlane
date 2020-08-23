<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WriteValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait LoadsAttributesFromEntryType
 *
 * @mixin Model
 * @package CbtechLtd\Fastlane\Support\Eloquent\Concerns
 */
trait LoadsAttributesFromEntryType
{
    public function loadAttributesFromEntryType(EntryInstance $entryInstance): void
    {
        $fields = Collection::make($entryInstance->schema()->getFields())
            ->filter(fn(SchemaField $field) => $field instanceof WriteValue)
            ->mapWithKeys(function (WriteValue $ft) {
                return $ft->toModelAttribute();
            });

        $this->mergeFillable($fields->except($this->fillable)->keys()->all());
        $this->mergeCasts($fields->except(array_keys($this->casts))->filter()->all());
    }
}
