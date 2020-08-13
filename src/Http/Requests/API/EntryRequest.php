<?php

namespace CbtechLtd\Fastlane\Http\Requests\API;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest as BaseEntryRequest;

class EntryRequest extends BaseEntryRequest
{
    protected function getUrlPrefix(): string
    {
        return config('fastlane.api.url_prefix') . '/entry-types';
    }
}
