<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\FileAttachment\AttachmentValue;
use CbtechLtd\Fastlane\FileAttachment\Contracts\PersistentAttachmentHandler;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\HandlesAttachments;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\HasAttachments;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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

    public function min(int $number): self
    {
        return $this->setRule('min', $number);
    }

    public function max(int $number): self
    {
        return $this->setRule('max', $number);
    }

    public function maxSize(int $size): self
    {
        return $this->setRule('size', $size);
    }

    public function readValue(EntryInstance $entryInstance): FieldValue
    {
        if (is_callable($this->readValueCallback)) {
            return call_user_func($this->readValueCallback, $entryInstance);
        }

        $files = Arr::wrap($entryInstance->model()->{$this->getName()});
        $handler = app()->make(config('fastlane.attachments.persistent_handler'));

        $value = $handler->read($entryInstance, $files)->map(
            fn(AttachmentValue $attachment) => $attachment->toArray()
        )->all();

        return new FieldValue($this->getName(), $value);
    }

    public function writeValue(EntryInstance $entryInstance, $value, array $requestData): void
    {
        if (! $this->isAcceptingAttachments() || ! $draftId = Arr::get($requestData, $this->getName() . '__draft_id')) {
            return;
        }

        if (is_callable($this->writeValueCallback)) {
            call_user_func($this->writeValueCallback, $entryInstance, $value, $requestData);
            return;
        }

        // TODO: Remove draft attachments with excluded=true

        /** @var PersistentAttachmentHandler $handler */
        $persistentHandler = app()->make(config('fastlane.attachments.persistent_handler'));

        $persistentHandler->write(
            $entryInstance,
            $this->getName(),
            Collection::make($value)->filter(fn($v) => ! $v['excluded'])->all(),
            $draftId

        );

        $persistentHandler->remove(
            $entryInstance,
            $this->getName(),
            Collection::make($value)->filter(fn($v) => $v['excluded'])->all(),
            $draftId
        );
    }

    public function toModelAttribute(): array
    {
        return [$this->getName() => 'array'];
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName()                 => 'array',
            $this->getName() . '.*'          => "present|array",
            $this->getName() . '.*.file'     => "required|string",
            $this->getName() . '.*.is_draft' => "required|boolean",
            $this->getName() . '.*.excluded' => "required|boolean",
            $this->getName() . '__draft_id'  => "required_with:{$this->getName()}|uuid",
        ];
    }

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'multiple'         => $this->multiple,
            'maxFileSize'      => $this->getRuleParams('size', config('fastlane.attachments.max_size')),
            'minNumberOfFiles' => $this->getRuleParams('min', $this->required ? 1 : 0),
            'maxNumberOfFiles' => $this->getRuleParams('max', $this->multiple ? null : 1),
            'fileTypes'        => $this->getAcceptableMimetypes(),
            'csrfToken'        => csrf_token(),
            'links'            => [
                'self' => URL::relative("cp.{$entryInstance->type()->identifier()}.attachments", $this->getName()),
            ],
        ]);
    }
}
