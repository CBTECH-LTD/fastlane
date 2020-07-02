<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\FastlaneFacade;
use Illuminate\Support\Str;

class EntryRequest extends FormRequest
{
    protected ?EntryType $entryType = null;

    public function entryType()
    {
        if (! $this->entryType) {
            $this->entryType = FastlaneFacade::entryTypes()->get(
                explode('/', Str::replaceFirst('cp/', '', $this->path()))[0]
            );
        }

        return $this->entryType;
    }

    public function authorize()
    {
        return ! is_null($this->entryType());
    }

    public function rules()
    {
        return [];
    }
}
