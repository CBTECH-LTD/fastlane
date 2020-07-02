<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EntryUpdateRequest extends FormRequest
{
    protected ?EntryType $entryCollection = null;

    public function entryType()
    {
        if (! $this->entryCollection) {
            $this->entryCollection = FastlaneFacade::entryTypes()->get(
                explode('/', Str::replaceFirst('cp/', '', $this->path()))[0]
            );
        }

        return $this->entryCollection;
    }

    public function authorize()
    {
        return ! is_null($this->entryType());
    }

    public function rules()
    {
        return Collection::make($this->entryType()->schema()->build()->toUpdate()->getFields())
            ->mapWithKeys(function (SchemaFieldType $fieldType) {
                $rules = $fieldType->getUpdateRules();

                return [
                    $fieldType->getName() => 'sometimes|' . $rules,
                ];
            })->all();
    }
}
