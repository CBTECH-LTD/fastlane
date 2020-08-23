<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use Illuminate\Http\Request;

class FastlaneRequest extends Request
{
    private EntryTypeContract $entryType;
    private EntryInstanceContract $entryInstance;

    /**
     * @return EntryTypeContract
     */
    public function getEntryType(): EntryTypeContract
    {
        return $this->entryType;
    }

    /**
     * @param EntryTypeContract $entryType
     * @return FastlaneRequest
     */
    public function setEntryType(EntryTypeContract $entryType): self
    {
        $this->entryType = $entryType;
        return $this;
    }

    public function setEntryInstance(EntryInstanceContract $entryInstance): self
    {
        $this->entryInstance = $entryInstance;
        return $this;
    }

    public function getEntryInstance(): EntryInstanceContract
    {
        return $this->entryInstance;
    }
}
