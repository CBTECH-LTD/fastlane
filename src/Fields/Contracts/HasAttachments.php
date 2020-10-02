<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Contracts;

interface HasAttachments
{
    public function getStorageDirectory(): string;

    public function getAcceptableMimetypes(): array;
}
