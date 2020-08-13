<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use Illuminate\Database\Eloquent\Model;

abstract class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    public function entryType(): EntryType
    {
        return app('fastlane')->getRequestEntryType();
    }

    public function entry(): Model
    {
        return app('fastlane')->getRequestEntry();
    }
}
