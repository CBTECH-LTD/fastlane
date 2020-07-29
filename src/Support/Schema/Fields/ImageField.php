<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use Illuminate\Support\Str;

class ImageField extends FileField
{
    protected array $accept = [
        'images/jpeg',
        'images/png',
        'images/gif',
    ];

    public function getType(): string
    {
        return 'image';
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

    protected function getTypeRules(): array
    {
        return [
            $this->getName()=> 'url|starts_with:' . $this->getBaseImageUrl(),
        ];
    }

    protected function getConfig(): array
    {
        return [
            'baseViewUrl' => $this->getBaseImageUrl(),
            'uploadUrl'   => config('fastlane.image_upload_url'),
        ];
    }

    public function fillModel($model, $value, EntryRequest $request): void
    {
        parent::fillModel(
            $model,
            Str::replaceFirst($this->getBaseImageUrl(), '', $value),
            $request
        );
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected function getBaseImageUrl()
    {
        return config('fastlane.thumbor_url');
    }

    protected function getListWidth(): int
    {
        return 150;
    }
}
