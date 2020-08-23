<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Eloquent\Concerns;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\RelationField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin Model
 * @package CbtechLtd\Fastlane\Support\Eloquent\Concerns]
 */
trait LoadsRelationsFromEntryType
{
    public function loadRelationsFromEntryType(EntryInstance $entryInstance): void
    {
        Collection::make($entryInstance->schema()->getFields())
            ->filter(fn(SchemaField $field) => $field instanceof RelationField)
            ->each(function (RelationField $ft) {
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
