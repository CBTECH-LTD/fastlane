<?php

namespace CbtechLtd\Fastlane\Http\Requests\Concerns;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasEntryType
{
    protected ?EntryType $entryType = null;
    protected ?Model $entry = null;

    public function entryType(): EntryType
    {
        return $this->entryType;
    }

    public function hasEntry(): bool
    {
        return $this->entry instanceof Model;
    }

    public function entry(): ?Model
    {
        return $this->entry;
    }

    public function authorize()
    {
        if (method_exists($this, 'authorizeRequest')) {
            return $this->authorizeRequest();
        }

        return true;
    }

    private function resolveEntryType(): void
    {
        $entryType = FastlaneFacade::getEntryTypeByIdentifier(
            explode('/', Str::replaceFirst('cp/', '', $this->path()))[0]
        );

        $this->resolveEntry($entryType);

        $this->entryType = $entryType->resolveForRequest($this);
    }

    private function resolveEntry(EntryType $entryType): void
    {
        if (! is_null($this->id)) {
            $this->entry = $entryType->findItem($this->id);
        }
    }
}
