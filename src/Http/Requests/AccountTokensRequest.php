<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\FastlaneFacade;

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
            'abilities.*' => 'required|in:' . implode(',', FastlaneFacade::getAccessTokenAbilities()),
        ];
    }
}
