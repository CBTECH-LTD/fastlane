<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;

class EntryAttachmentStoreRequest extends FormRequest
{
    protected bool $hasLoadedField = false;
    protected ?HasAttachments $field = null;

    public function authorize()
    {
        if (is_null($this->field())) {
            return false;
        }

        return $this->field() instanceof HasAttachments && $this->field()->isAcceptingAttachments();
    }

    public function field(): ?HasAttachments
    {
        if ($this->hasLoadedField) {
            return $this->field;
        }

        /** @var HasAttachments | null field */
        $this->field = $this->entryType()->schema()->findField($this->fieldName);
        $this->hasLoadedField = true;

        return $this->field;
    }

    public function rules()
    {
        return [];
    }
}
