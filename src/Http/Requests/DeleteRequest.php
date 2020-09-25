<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Requests;

use Illuminate\Support\Facades\Gate;

class DeleteRequest extends UpdateRequest
{
    public function rules()
    {
        return [];
    }

    public function authorize(): bool
    {
        if (Gate::getPolicyFor($this->entryType::model())) {
            return $this->user()->can('delete', $this->entryType->modelInstance());
        }

        return true;
    }
}
