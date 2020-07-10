<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Support\Collection;

class EntryUpdateRequest extends EntryRequest
{
    public function rules()
    {
        return Collection::make($this->entryType()->schema()->getDefinition()->toUpdate()->getFields())
            ->mapWithKeys(function (SchemaFieldType $fieldType) {
                $rules = $fieldType->getUpdateRules();

                return [
                    $fieldType->getName() => 'sometimes|' . $rules,
                ];
            })->all();
    }
}
