<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\EntryTypes\FileManager\File;
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

    public function isMultiple(): bool
    {
        return $this->multiple;
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

        $files = File::query()->whereKey(Arr::wrap($entryInstance->model()->{$this->getName()}))->get();

        return $this->buildFieldValueInstance($this->getName(), $files->toArray());
    }

    public function toModelAttribute(): array
    {
        return [$this->getName() => 'array'];
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName()        => 'array',
            $this->getName() . '.*' => "present|array",
            $this->getName() . '.*' => "required|exists:fastlane_files,id",
        ];
    }

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'multiple'         => $this->isMultiple(),
            'maxFileSize'      => $this->getRuleParams('size', config('fastlane.attachments.max_size')),
            'minNumberOfFiles' => $this->getRuleParams('min', $this->required ? 1 : 0),
            'maxNumberOfFiles' => $this->getRuleParams('max', $this->isMultiple() ? null : 1),
            'fileTypes'        => $this->getAcceptableMimetypes(),
            'csrfToken'        => csrf_token(),
            'links'            => [
                'fileManager' => URL::relative("cp.file-manager.index"),
            ],
        ]);
    }

    protected function buildFieldValueInstance(string $fieldName, $value): FieldValue
    {
        return new FileFieldValue($fieldName, $value);
    }
}
