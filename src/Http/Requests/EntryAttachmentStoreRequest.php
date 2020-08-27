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
        $field = $this->entryInstance()->schema()->findField($this->fieldName);

        if ($field instanceof HasAttachments) {
            $this->field = $field;
        }

        $this->hasLoadedField = true;
        return $this->field;
    }

    public function rules()
    {
        return [
            'draft_id' => 'required|uuid',
            'name'     => 'required|string',
            'files'    => 'required|array|size:1',
            'files.*'  => "required|file{$this->getMimetypeRule()}",
        ];
    }

    protected function getMimetypeRule(): string
    {
        if (empty($acceptRules = $this->field->getAcceptableMimetypes())) {
            return '';
        }

        return '|mimetypes:' . implode(',', $acceptRules);
    }
}
