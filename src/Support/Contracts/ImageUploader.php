<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Contracts\EntryType;
use Illuminate\Http\UploadedFile;

interface ImageUploader
{
    public function getImageRules(): string;

    public function processImageUpload(UploadedFile $file, EntryType $entryType): string;

    public function prepareValueToFill($value, array $requestData): string;

    public function getImageUrl(string $image): string;
}
