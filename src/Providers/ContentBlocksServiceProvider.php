<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\Facades\ContentBlock;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class ContentBlocksServiceProvider extends ServiceProvider
{
    public function register()
    {
        Collection::make(config('fastlane.content_blocks'))
            ->each(function (string $class) {
                ContentBlock::register($class);
            });
    }
}
