<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Fastlane;
use Illuminate\Foundation\Http\FormRequest;

class AccountTokensRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if ($this->isMethod('get')) {
            return [];
        }

        return [
            'name'        => 'required|string|min:3',
            'abilities'   => 'required|array',
            'abilities.*' => 'required|in:' . implode(',', Fastlane::getAccessTokenAbilities()),
        ];
    }
}
