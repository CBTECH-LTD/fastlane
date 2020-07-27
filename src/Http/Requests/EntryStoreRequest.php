<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Support\Collection;

class EntryStoreRequest extends EntryRequest
{
    public function rules()
    {
        return Collection::make($this->entryType()->schema()->toCreate())
            ->mapWithKeys(function (SchemaField $fieldType) {
                return [
                    $fieldType->getName() => $fieldType->getCreateRules(),
                ];
            })->all();
    }
}
