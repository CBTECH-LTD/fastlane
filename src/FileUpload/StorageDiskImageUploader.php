<?php

namespace CbtechLtd\Fastlane\FileUpload;

use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\ImageUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageDiskImageUploader implements ImageUploader
{
    public function processImageUpload(UploadedFile $file, EntryType $entryType): string
    {
        $filename = Storage::disk($this->getDisk())->putFile(
            $entryType->identifier(),
            $file,
            'public'
        );

        return Storage::disk($this->getDisk())->url($filename);
    }

    public function getImageRules(): string
    {
        return 'url|starts_with:' . $this->getBaseImageUrl();
    }

    public function prepareValueToFill($value, array $requestData): string
    {
        return Str::replaceFirst($this->getBaseImageUrl(), '', $value);
    }

    public function getImageUrl(string $image): string
    {
        return Storage::disk($this->getDisk())->url($image);
    }

    public function acceptableMimetypes(): array
    {
        return [
            'images/jpeg',
            'images/png',
            'images/gif',
        ];
    }

    protected function getBaseImageUrl(): string
    {
        return config('filesystems.disks.' . $this->getDisk() . '.url');
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDisk()
    {
        return config('fastlane.image_uploader.disk');
    }
}
