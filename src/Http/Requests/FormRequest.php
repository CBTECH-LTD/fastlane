<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;

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
        return app('fastlane')->getRequest()->getEntryType();
    }

    public function entryInstance(): EntryInstance
    {
        return app('fastlane')->getRequest()->getEntryInstance();
    }
}
