<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Schema\Fields\ImageField;

class EntryImageStoreRequest extends EntryRequest
{
    protected ?ImageField $field = null;

    public function authorizeRequest()
    {
        return $this->field() instanceof ImageField;
    }

    public function field(): ImageField
    {
        if ($this->field instanceof ImageField) {
            return $this->field;
        }

        $field = $this->entryType()->schema()->findField($this->fieldName);

        if (! $field instanceof ImageField) {
            abort(404, 'Field does not accept images.');
        }

        $this->field = $field;
        return $this->field;
    }
}
