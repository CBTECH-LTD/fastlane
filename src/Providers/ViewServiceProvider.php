<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\View\Components\AppLayout;
use CbtechLtd\Fastlane\View\Components\BoxedCard;
use CbtechLtd\Fastlane\View\Components\Button;
use CbtechLtd\Fastlane\View\Components\Field;
use CbtechLtd\Fastlane\View\Components\Icon;
use CbtechLtd\Fastlane\View\Components\Link;
use CbtechLtd\Fastlane\View\Components\MenuLink;
use CbtechLtd\Fastlane\View\Components\MenuWrapper;
use CbtechLtd\Fastlane\View\Components\Spinner;
use CbtechLtd\Fastlane\View\Components\TableCard;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap your package's services.
     */
    public function boot(): void
    {
        $components = [
            AppLayout::class,
            Button::class,
            Icon::class,
            Link::class,
            Spinner::class,

            // Cards
            BoxedCard::class,
            TableCard::class,

            // Menu
            MenuWrapper::class,
            MenuLink::class,

            // Form components
            Field::class,
        ];

        foreach ($components as $component) {
            $class = (new \ReflectionClass($component))->getShortName();

            Blade::component($component, 'fl-' . Str::snake($class, '-'));
        }
    }
}
