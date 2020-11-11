<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use Illuminate\Support\AggregateServiceProvider;

class FastlaneServiceProvider extends AggregateServiceProvider
{
    protected $providers = [
        AppServiceProvider::class,
        AuthServiceProvider::class,
        ViewServiceProvider::class,
        EntryTypesServiceProvider::class,
        ContentBlocksServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
