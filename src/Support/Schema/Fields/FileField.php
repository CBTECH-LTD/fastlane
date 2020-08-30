<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\FileAttachment\Attachment;
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
    protected bool $multiple = false;

    public function getType(): string
    {
        return 'file';
    }

    public function getAcceptableMimetypes(): array
    {
        return $this->accept;
    }

    public function accept(array $accept): self
    {
        $this->accept = $accept;
        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    public function readValue(EntryInstance $entryInstance): FieldValue
    {
        if (is_callable($this->readValueCallback)) {
            return call_user_func($this->readValueCallback, $entryInstance);
        }

        $attachment = Attachment::query()
            ->where('file', $entryInstance->model()->{$this->getName()})
            ->where('attachable_type', get_class($entryInstance->model()))
            ->where('attachable_id', $entryInstance->model()->getKey())
            ->first();

        $value = $attachment instanceof Attachment
            ? [
                'file' => $attachment->file,
                'name' => $attachment->name,
                'url'  => $attachment->url,
            ]
            : null;

        return new FieldValue($this->getName(), $value);
    }

    public function writeValue(EntryInstance $entryInstance, $value, array $requestData): void
    {
        if (is_callable($this->writeValueCallback)) {
            call_user_func($this->writeValueCallback, $entryInstance, $value[0], $requestData);
            return;
        }

        // TODO: We need to improve it. How to save multiple on database?
        //       Should it be a json object? A text with some character to separate files?
        $entryInstance->model()->{$this->getName()} = $this->multiple
            ? $value
            : $value[0];

        // Check whether files are accepted in the rich editor instance,
        // then persist all draft attachment files.
        $entryInstance->type()->addHook(EntryType::HOOK_AFTER_SAVING, function (OnSavingHook $hook) use ($requestData, $entryInstance, $value) {
            if ($this->isAcceptingAttachments() && $draftId = Arr::get($requestData, $this->getName() . '__draft_id')) {
                $attachments = DraftAttachment::query()
                    ->where('file', $value)
                    ->where('draft_id', $draftId)
                    ->get()
                    ->map(function (DraftAttachment $draft) use ($draftId, $entryInstance) {
                        return $draft->persist($this, $entryInstance->model());
                    });
            }
        });
    }

    protected function getTypeRules(): array
    {
        return [
            // TODO: Improve the URL stuff. Something like ImageField and ImageUploader would be better.
            $this->getName()                => 'array',
            $this->getName() . '.*'         => "sometimes|exists:fastlane_draft_attachments,file",
            $this->getName() . '__draft_id' => "required_with:{$this->getName()}|uuid",
        ];
    }

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'multiple'  => $this->multiple,
            'fileTypes' => $this->getAcceptableMimetypes(),
            'csrfToken' => csrf_token(),
            'links'     => [
                'self' => URL::relative("cp.{$entryInstance->type()->identifier()}.attachments", $this->getName()),
            ],
        ]);
    }
}
