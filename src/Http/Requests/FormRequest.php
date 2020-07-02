<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use App\CMS\DTO\IncomingData;

abstract class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function getIncomingData(): IncomingData
    {
        return new IncomingData($this->validator->validated());
    }
}
