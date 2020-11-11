<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\Support\Menu\MenuLink;
use CbtechLtd\Fastlane\View\Components\AppLayout;
use CbtechLtd\Fastlane\View\Components\BoxedCard;
use CbtechLtd\Fastlane\View\Components\Button;
use CbtechLtd\Fastlane\View\Components\Field;
use CbtechLtd\Fastlane\View\Components\FlashMessages;
use CbtechLtd\Fastlane\View\Components\Form;
use CbtechLtd\Fastlane\View\Components\Icon;
use CbtechLtd\Fastlane\View\Components\ItemActionDelete;
use CbtechLtd\Fastlane\View\Components\Link;
use CbtechLtd\Fastlane\View\Components\Listing;
use CbtechLtd\Fastlane\View\Components\ListingItemAction;
use CbtechLtd\Fastlane\View\Components\MenuWrapper;
use CbtechLtd\Fastlane\View\Components\Paginator;
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
        $this->registerBladeComponents();
        $this->registerLivewireComponents();
    }

    protected function registerBladeComponents(): void
    {
        $components = [
            AppLayout::class,
            Button::class,
            Icon::class,
            Link::class,
            ListingItemAction::class,
            Paginator::class,
            Spinner::class,

            // Cards
            BoxedCard::class,
            TableCard::class,

            // Menu
            MenuWrapper::class,
            MenuLink::class,

            // Form components
            Field::class,
            Form\Panel::class,
            Form\Select::class,
            Form\ShortText::class,
            Form\Toggle::class,

            // Listing components
            Listing\RowCellRenderer::class,
            Listing\Boolean::class,
            Listing\ShortText::class,
            Listing\Select::class,
        ];

        foreach ($components as $component) {
            Blade::component($component, $component::tag());
        }
    }

    protected function registerLivewireComponents(): void
    {
        $components = [
            FlashMessages::class,
            ItemActionDelete::class,

            // Form
            Form\Form::class,

            // Listing
            Listing\ReactiveToggle::class,
        ];

        foreach ($components as $component) {
            Livewire::component($component::tag(), $component);
        }
    }
}
