<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FastlaneRequest extends Request
{
    private EntryTypeContract $entryType;
    private ?Model $entryInstance = null;

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
        $this->entryType = $entryType->resolve($this->all());
        return $this;
    }

    /**
     * @return Model|null
     */
    public function getEntryInstance(): ?Model
    {
        return $this->entryInstance;
    }

    /**
     * @param Model|null $entryInstance
     * @return FastlaneRequest
     */
    public function setEntryInstance(?Model $entryInstance): self
    {
        $this->entryInstance = $entryInstance;
        return $this;
    }

    public function hasEntryInstance(): bool
    {
        return $this->entryInstance instanceof Model;
    }
}
