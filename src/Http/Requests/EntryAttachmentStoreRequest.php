<?php

namespace CbtechLtd\Fastlane\Http\Requests;

use CbtechLtd\Fastlane\Support\Contracts\SchemaFieldType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Concerns\HandlesAttachments;

class EntryAttachmentStoreRequest extends EntryRequest
{
    protected bool $hasLoadedField = false;
    protected ?SchemaFieldType $field = null;

    public function authorizeRequest()
    {
        if (is_null($this->field())) {
            return false;
        }

        return $this->field() instanceof SchemaFieldType
            && in_array(HandlesAttachments::class, class_uses($this->field()))
            && $this->field()->isAcceptingFiles();
    }

    public function field(): ?SchemaFieldType
    {
        if ($this->hasLoadedField) {
            return $this->field;
        }

        $this->field = $this->entryType()->schema()->getDefinition()->findField($this->fieldName);
        $this->hasLoadedField = true;

        return $this->field;
    }
}
