<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Support\Contracts\EntryType as EntryTypeContract;
use CbtechLtd\Fastlane\Support\Contracts\ImageUploader;
use CbtechLtd\Fastlane\Support\Schema\Fields\Concerns\ExportsToApiAttribute;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\ExportsToApiAttribute as ExportsToApiAttributeContract;
use Illuminate\Database\Eloquent\Model;
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
            app('fastlane')->getRequestEntryType()
        );
    }

    protected function getTypeRules(): array
    {
        return [
            $this->getName() => $this->uploader->getImageRules(),
        ];
    }

    public function fillModel($model, $value, array $requestData): void
    {
        parent::fillModel(
            $model,
            $this->uploader->prepareValueToFill($value, $requestData),
            $requestData
        );
    }

    public function resolveValue(Model $model): array
    {
        if ($this->resolveValueCallback) {
            return call_user_func($this->resolveValueCallback, $model);
        }

        return [
            $this->getName() => $this->uploader->getImageUrl($model->{$this->getName()}),
        ];
    }

    protected function resolveConfig(EntryTypeContract $entryType, array $data): array
    {
        return [
            'uploadUrl' => route("cp.{$entryType->identifier()}.images", $this->getName()),
        ];
    }
}
