<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;

class FileField extends AbstractBaseField implements ExportsToApiAttributeContract, HasAttachments
{
    use ExportsToApiAttribute, HandlesAttachments;

    protected array $accept = [];

    public function getType(): string
    {
        return 'file';
    }

    public function getAccept(): array
    {
        return $this->accept;
    }

    public function accept(array $accept): self
    {
        $this->accept = $accept;
        return $this;
    }

    public function fillModel($model, $value, array $requestData): void
    {
        if (is_callable($this->fillValueCallback)) {
            call_user_func($this->fillValueCallback, $model, $value[0], $requestData);
            return;
        }

        $model->{$this->getName()} = $value[0];

        // Check whether files are accepted in the rich editor instance,
        // then persist all draft attachment files.
        if ($this->isAcceptingAttachments() && $draftId = Arr::get($requestData, $this->getName() . '__draft_id')) {
            DraftAttachment::persistAllDraft(
                $draftId,
                $this,
                $model,
            );
        }
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => 'file',
        ];
    }

    protected function resolveConfig(EntryTypeContract $entryType, array $data): array
    {
        return [
            'fileTypes' => $this->getAccept(),
            'csrfToken' => csrf_token(),
            'links'     => [
                'self' => URL::relative("cp.{$entryType->identifier()}.attachments", $this->getName()),
            ],
        ];
    }
}
