<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Support\Collection;

class EntryUpdateRequest extends EntryRequest
{
    public function rules()
    {
        return Collection::make($this->entryType()->schema()->toUpdate())
            ->mapWithKeys(function (SchemaField $fieldType) {
                $rules = $fieldType->getUpdateRules();

                return [
                    $fieldType->getName() => 'sometimes|' . $rules,
                ];
            })->all();
    }
}
