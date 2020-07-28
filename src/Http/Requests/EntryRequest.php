<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Http\Requests\Concerns\HasEntryType;

class EntryRequest extends FormRequest
{
    use HasEntryType;

    public function rules()
    {
        return [];
    }

    protected function prepareForValidation()
    {
        $this->resolveEntryType();
    }
}
