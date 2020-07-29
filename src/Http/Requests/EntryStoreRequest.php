<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Support\Collection;

class EntryStoreRequest extends EntryRequest
{
    public function rules()
    {
        return Collection::make($this->entryType()->schema()->getCreateFields())
            ->mapWithKeys(fn(SchemaField $fieldType) => $fieldType->getCreateRules())
            ->all();
    }
}
