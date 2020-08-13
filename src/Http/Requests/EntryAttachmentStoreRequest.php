<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;

class EntryAttachmentStoreRequest extends FormRequest
{
    protected bool $hasLoadedField = false;
    protected ?SchemaField $field = null;

    public function authorizeRequest()
    {
        if (is_null($this->field())) {
            return false;
        }

        return $this->field() instanceof SchemaField
            && in_array(HandlesAttachments::class, class_uses($this->field()))
            && $this->field()->isAcceptingFiles();
    }

    public function field(): ?SchemaField
    {
        if ($this->hasLoadedField) {
            return $this->field;
        }

        $this->field = $this->entryType()->schema()->findField($this->fieldName);
        $this->hasLoadedField = true;

        return $this->field;
    }

    public function rules()
    {
        return [];
    }
}
