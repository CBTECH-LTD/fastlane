<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

use Illuminate\Http\Request;

interface HasAttachments
{
    public function storeAttachment(Request $request): string;

    public function getStorageDirectory(): string;

    public function isAcceptingAttachments(): bool;

    public function acceptAttachments($accept = true): self;

    public function getAcceptableMimetypes(): array;
}
