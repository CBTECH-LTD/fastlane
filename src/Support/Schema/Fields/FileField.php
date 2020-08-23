<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
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

    public function writeValue(EntryInstance $entryInstance, $value, array $requestData): void
    {
        if (is_callable($this->writeValueCallback)) {
            call_user_func($this->writeValueCallback, $entryInstance, $value[0], $requestData);
            return;
        }

        $entryInstance->model()->{$this->getName()} = $value[0];

        // Check whether files are accepted in the rich editor instance,
        // then persist all draft attachment files.
        if ($this->isAcceptingAttachments() && $draftId = Arr::get($requestData, $this->getName() . '__draft_id')) {
            DraftAttachment::persistAllDraft(
                $draftId,
                $this,
                $entryInstance->model(),
            );
        }
    }

    protected function getTypeRules(): array
    {
        return [
            // TODO: Improve the URL stuff. Something like ImageField and ImageUploader would be better.
            $this->getName()        => 'array',
            $this->getName() . '.*' => "sometimes|url|starts_with:" . url('storage/attachments'),
        ];
    }

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'fileTypes' => $this->getAccept(),
            'csrfToken' => csrf_token(),
            'links'     => [
                'self' => URL::relative("cp.{$entryInstance->type()->identifier()}.attachments", $this->getName()),
            ],
        ]);
    }
}
