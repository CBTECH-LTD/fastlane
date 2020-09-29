<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\View\Components\AppLayout;
use CbtechLtd\Fastlane\View\Components\BoxedCard;
use CbtechLtd\Fastlane\View\Components\Button;
use CbtechLtd\Fastlane\View\Components\Field;
use CbtechLtd\Fastlane\View\Components\Icon;
use CbtechLtd\Fastlane\View\Components\Link;
use CbtechLtd\Fastlane\View\Components\Listing;
use CbtechLtd\Fastlane\View\Components\ListingItemAction;
use CbtechLtd\Fastlane\View\Components\MenuLink;
use CbtechLtd\Fastlane\View\Components\MenuWrapper;
use CbtechLtd\Fastlane\View\Components\Paginator;
use CbtechLtd\Fastlane\View\Components\ReactiveComponent;
use CbtechLtd\Fastlane\View\Components\Spinner;
use CbtechLtd\Fastlane\View\Components\TableCard;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
            ListingItemAction::class,
            Paginator::class,

            // Cards
            BoxedCard::class,
            TableCard::class,

            // Menu
            MenuWrapper::class,
            MenuLink::class,

            // Form components
            Field::class,

            // Listing components
            Listing\RowCellRenderer::class,
            Listing\Boolean::class,
            Listing\ShortText::class,

            // Reactive components
            Listing\ReactiveToggle::class,
        ];

        foreach ($components as $component) {
            if (is_a($component, ReactiveComponent::class, true)) {
                Livewire::component($component::tag(), $component);
                continue;
            }

            Blade::component($component, $component::tag());
        }
    }
}
