<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Fields\Field;
use CbtechLtd\Fastlane\QueryFilter\QueryFilter;
use CbtechLtd\Fastlane\QueryFilter\QueryFilterContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class IndexRequest extends FastlaneRequest
{
    public function authorize(): bool
    {
        if (Gate::getPolicyFor($this->entryType::model())) {
            return $this->user()->can('list', $this->entryType::model());
        }

        return true;
    }

    public function rules()
    {
        return [];
    }

    public function buildQueryFilter(): QueryFilterContract
    {
        return tap(new QueryFilter(), function (QueryFilter $qf) {
            if ($order = $this->input('order')) {
                $fieldName = Str::replaceFirst('-', '', $order);

                $sortable = Collection::make($this->entryType->getFields()->onListing())
                    ->filter(fn(Field $field) => $field->isSortable())
                    ->first(fn(Field $field) => $field->isSortable() && $field->getAttribute() === $fieldName);

                if ($sortable) {
                    $qf->withOrder($order);
                }
            }
        });
    }

    protected function makeEntryType($class): EntryTypeContract
    {
        return $class::newInstance();
    }
}
