<?php

namespace CbtechLtd\Fastlane\FileUpload;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\ImageUploader;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ThumborImageUploader implements ImageUploader
{
    public function processImageUpload(UploadedFile $file, EntryType $entryType): string
    {
        $client = new Client;
        $url = config('fastlane.image_uploader.upload_url');

        $response = $client->request('POST', $url, [
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'type'     => $file->getMimeType(),
                    'filename' => $file->getFilename(),
                ],
            ],
        ]);

        return $this->buildImageUrl($response->getHeader('Location')[0]);
    }

    public function acceptableMimetypes(): array
    {
        return [
            'images/jpeg',
            'images/png',
            'images/gif',
        ];
    }

    public function prepareValueToFill(EntryRequest $request, $value): string
    {
        return Str::replaceFirst($this->getBaseImageUrl(), '', $value);
    }

    public function getImageRules(): string
    {
        return 'url|starts_with:' . $this->getBaseImageUrl();
    }

    protected function buildImageUrl(string $image): string
    {
        return "{$this->getBaseImageUrl()}{$image}";
    }

    protected function getBaseImageUrl(): string
    {
        return config('fastlane.image_uploader.image_url');
    }
}
