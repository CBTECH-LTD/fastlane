<?php

namespace CbtechLtd\Fastlane\Support\Contracts;

use CbtechLtd\Fastlane\Http\Requests\EntryRequest;
use Illuminate\Http\UploadedFile;

interface ImageUploader
{
    public function getImageRules(): string;

    public function processImageUpload(UploadedFile $file, EntryType $entryType): string;

    public function prepareValueToFill(EntryRequest $request, $value): string;

    public function getImageUrl(string $image): string;
}
