<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\SupportModel;
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
    public function loadAttributesFromEntryType(EntryType $entryType): void
    {
        $fields = Collection::make($entryType->schema()->getFields())
            ->filter(fn(SchemaField $field) => $field instanceof SupportModel)
            ->mapWithKeys(function (SupportModel $ft) {
                return $ft->toModelAttribute();
            });

        $this->mergeFillable($fields->except($this->fillable)->keys()->all());
        $this->mergeCasts($fields->except(array_keys($this->casts))->filter()->all());
    }
}
