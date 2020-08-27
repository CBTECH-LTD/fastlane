<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\ImageUploader;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use Illuminate\Http\Request;

class ImageField extends FileField implements ExportsToApiAttributeContract
{
    use ExportsToApiAttribute;

    protected ImageUploader $uploader;

    protected int $listWidth = 150;

    public function __construct(string $name, string $label)
    {
        parent::__construct($name, $label);
        $this->uploader = app()->make(config('fastlane.image_uploader.class'));
    }

    public function getType(): string
    {
        return 'image';
    }

    public function withUploader(ImageUploader $uploader): self
    {
        $this->uploader = $uploader;
        return $this;
    }

    public function processUpload(Request $request): string
    {
        return $this->uploader->processImageUpload(
            $request->file($this->getName()),
            app('fastlane')->getRequest()->getEntryType()
        );
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => $this->uploader->getImageRules(),
        ];
    }

    public function writeValue($model, $value, array $requestData): void
    {
        parent::writeValue(
            $model,
            [$this->uploader->prepareValueToFill($value, $requestData)],
            $requestData
        );
    }

    public function readValue(EntryInstance $entryInstance): FieldValue
    {
        if ($this->readValueCallback) {
            return call_user_func($this->readValueCallback, $entryInstance);
        }

        $modelValue = $entryInstance->model()->{$this->getName()};

        return new FieldValue(
            $this->getName(),
            $modelValue ? $this->uploader->getImageUrl($modelValue) : ''
        );
    }

    protected function resolveConfig(EntryInstance $entryInstance, string $destination): void
    {
        $this->resolvedConfig = $this->resolvedConfig->merge([
            'uploadUrl' => route("cp.{$entryInstance->type()->identifier()}.images", $this->getName()),
        ]);
    }
}
