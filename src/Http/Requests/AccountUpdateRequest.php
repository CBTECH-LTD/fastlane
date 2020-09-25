<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\Facades\EntryType as EntryTypeFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AccountUpdateRequest extends FastlaneRequest
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
        return $class::newInstance(Auth::user());
    }

    /**
     * @return EntryTypeContract|string
     */
    protected function findEntryTypeClass(): string
    {
        return EntryTypeFacade::findByKey(BackendUserEntryType::key());
    }
}
