<?php

namespace CbtechLtd\Fastlane\Http\Requests\Concerns;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;

trait HasEntryType
{
    public function entryType(): EntryType
    {
        return FastlaneFacade::getRequestEntryType();
    }

    public function hasEntry(): bool
    {
        return $this->entry() instanceof Model;
    }

    public function entry(): ?Model
    {
        return FastlaneFacade::getRequestEntry();
    }

    public function authorize()
    {
        if (method_exists($this, 'authorizeRequest')) {
            return $this->authorizeRequest();
        }

        return true;
    }
}
