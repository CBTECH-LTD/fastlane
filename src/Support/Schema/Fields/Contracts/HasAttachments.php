<?php

namespace CbtechLtd\Fastlane\Support\Schema\Fields\Contracts;

interface HasAttachments
{
    public function getStorageDirectory(): string;

    public function getAcceptableMimetypes(): array;
}
