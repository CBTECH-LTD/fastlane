<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Fields;

class Image extends File
{
    protected string $component = 'image';

    protected array $accept = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];
}
