<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Support\Collection;

class EntryStoreRequest extends EntryRequest
{
    public function rules()
    {
        return Collection::make($this->entryType()->schema()->getDefinition()->toCreate()->getFields())
            ->mapWithKeys(function (SchemaFieldType $fieldType) {
                return [
                    $fieldType->getName() => $fieldType->getCreateRules(),
                ];
            })->all();
    }
}
