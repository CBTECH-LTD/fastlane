<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Support\Collection;

class EntryUpdateRequest extends EntryRequest
{
    public function rules()
    {
        return Collection::make($this->entryType()->schema()->getUpdateFields())
            ->mapWithKeys(fn(SchemaField $fieldType) => $fieldType->getUpdateRules())
            ->all();
    }
}
