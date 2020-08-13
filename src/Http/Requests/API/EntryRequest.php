<?php

namespace CbtechLtd\Fastlane\Http\Requests\API;

use CbtechLtd\Fastlane\Http\Requests\FormRequest;

class EntryRequest extends FormRequest
{
    protected function getUrlPrefix(): string
    {
        return config('fastlane.api.url_prefix') . '/entry-types';
    }

    public function rules()
    {
        // TODO: Implement rules() method.
    }
}
