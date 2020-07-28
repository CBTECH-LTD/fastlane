<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\FileAttachment\StoreDraftAttachment;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;
use Illuminate\Support\Facades\URL;

class RichEditorField extends BaseSchemaField
{
    use HandlesAttachments;

    protected bool $acceptFiles = false;

    public function getType(): string
    {
        return 'richEditor';
    }

    public function isAcceptingFiles(): bool
    {
        return $this->acceptFiles;
    }

    public function acceptFiles(bool $state = true): self
    {
        $this->acceptFiles = $state;

        $this->addFile($state ? new StoreDraftAttachment($this) : null);

        return $this;
    }

    public function hydrateValue($model, $value, EntryRequest $request): void
    {
        if (is_callable($this->hydrateCallback)) {
            call_user_func($this->hydrateCallback, $request, $value, $model);
            return;
        }

        $model->{$this->getName()} = $value;

        // Check whether files are accepted in the rich editor instance,
        // then persist all draft attachment files.
        if ($this->isAcceptingFiles() && $draftId = $request->input($this->getName() . '__draft_id')) {
            DraftAttachment::persistAllDraft(
                $draftId,
                $this,
                $model,
            );
        }
    }

    protected function resolveConfig(EntryTypeContract $entryType, EntryRequest $request): array
    {
        return [
            'acceptFiles' => $this->acceptFiles,
            'links'       => [
                'self' => URL::relative("cp.{$entryType->identifier()}.attachments", $this->getName()),
            ],
        ];
    }

    protected function getMigrationMethod(): array
    {
        return ['longText'];
    }
}
