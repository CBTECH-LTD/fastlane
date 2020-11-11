<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields\Types;

class Image extends File
{
    protected string $formComponent = 'image';

    protected array $accept = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];
}
