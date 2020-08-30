<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use CbtechLtd\Fastlane\FileAttachment\DraftAttachment;
use CbtechLtd\Fastlane\Http\Requests\EntryAttachmentStoreRequest;

interface HasAttachments
{
    public function storeAttachment(EntryAttachmentStoreRequest $request): DraftAttachment;

    public function getStorageDirectory(): string;

    public function isAcceptingAttachments(): bool;

    public function acceptAttachments($accept = true): self;

    public function getAcceptableMimetypes(): array;
}
