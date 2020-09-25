<?php

namespace CbtechLtd\Fastlane\Http\Requests\Concerns;

use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Fastlane;
use Illuminate\Database\Eloquent\Model;

trait HasEntryType
{
    public function entryType(): EntryType
    {
        return Fastlane::getRequest()->getEntry();
    }

    public function hasEntry(): bool
    {
        return $this->entry() instanceof Model;
    }

    public function entry(): ?Model
    {
        return Fastlane::getRequest()->getEntryInstance();
    }

    public function authorize()
    {
        if (method_exists($this, 'authorizeRequest')) {
            return $this->authorizeRequest();
        }

        return true;
    }
}
