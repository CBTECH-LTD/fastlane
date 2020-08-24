<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\QueryFilter\QueryFilter;
use CbtechLtd\Fastlane\QueryFilter\QueryFilterContract;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance as EntryInstanceContract;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

    public function buildQueryFilter(): QueryFilterContract
    {
        return tap(new QueryFilter(), function (QueryFilter $qf) {

            if ($order = $this->input('order')) {
                $fieldName = Str::replaceFirst('-', '', $order);

                $sortable = Collection::make($this->getEntryInstance()->schema()->getIndexFields())
                    ->filter(fn(SchemaField $field) => $field->isSortable())
                    ->first(fn(SchemaField $field) => $field->isSortable() && $field->getName() === $fieldName);

                if ($sortable) {
                    $qf->withOrder($order);
                }
            }
        });
    }
}
