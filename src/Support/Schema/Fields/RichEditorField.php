<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\FileAttachment\StoreDraftAttachment;
use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class RichEditorField extends AbstractBaseField implements ExportsToApiAttributeContract
{
    use HandlesAttachments, ExportsToApiAttribute;

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

    public function fillModel($model, $value, EntryRequest $request): void
    {
        if (is_callable($this->fillValueCallback)) {
            call_user_func($this->fillValueCallback, $request, $value, $model);
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

    public function toApiAttribute(Model $model, array $options = [])
    {
        if ($this->toApiAttributeCallback) {
            return call_user_func($this->toApiAttributeCallback, $model);
        }

        if (Arr::get($options, 'output', 'collection') === 'collection') {
            return [];
        }

        return Collection::make($this->resolveValue($model))->mapWithKeys(
            fn($value, $key) => [
                $key => Arr::get($options, 'output', 'collection') === 'collection'
                    ? null
                    : $value,
            ]
        )->all();
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
