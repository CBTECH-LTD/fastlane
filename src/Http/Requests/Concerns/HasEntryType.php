<?php

namespace CbtechLtd\Fastlane\Http\Requests\Concerns;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Support\Str;

trait HasEntryType
{
    protected ?EntryType $entryType = null;

    public function entryType()
    {
        if (! $this->entryType) {
            $this->entryType = FastlaneFacade::getEntryTypeByIdentifier(
                explode('/', Str::replaceFirst('cp/', '', $this->path()))[0]
            );
        }

        return $this->entryType;
    }

    public function authorize()
    {
        if (is_null($this->entryType())) {
            return false;
        }

        if (method_exists($this, 'authorizeRequest')) {
            return $this->authorizeRequest();
        }

        return true;
    }
}
