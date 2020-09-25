<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Contracts\EntryType;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FastlaneRequest
{
    public function authorize(): bool
    {
        if (Gate::getPolicyFor($this->entryType::model())) {
            return $this->user()->can('update', $this->entryType->modelInstance());
        }

        return true;
    }

    public function rules()
    {
        return [];
    }

    protected function makeEntryType($class): EntryType
    {
        if (! $entry = $class::queryEntry($this->route()->parameter('id'))) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return $entry;
    }
}
