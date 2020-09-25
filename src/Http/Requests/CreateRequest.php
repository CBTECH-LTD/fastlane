<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use Illuminate\Support\Facades\Gate;

class CreateRequest extends FastlaneRequest
{
    public function rules()
    {
        return [];
    }

    public function authorize(): bool
    {
        if (Gate::getPolicyFor($this->entryType::model())) {
            return $this->user()->can('create', $this->entryType::model());
        }

        return true;
    }

    protected function makeEntryType($class): EntryTypeContract
    {
        return $class::newInstance();
    }
}
