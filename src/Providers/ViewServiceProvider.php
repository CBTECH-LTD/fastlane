<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\Support\Menu\MenuLink;
use CbtechLtd\Fastlane\View\Components\AppLayout;
use CbtechLtd\Fastlane\View\Components\AppMainArea;
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
use Livewire\Livewire as LivewireFacade;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap your package's services.
     */
    public function boot(): void
    {
        $this->registerComponents();
    }

    protected function registerComponents(): void
    {
        $components = [
            AppLayout::class,
            AppMainArea::class,
            Button::class,
            FlashMessages::class,
            Icon::class,
            ItemActionDelete::class,
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
            Form\CreateForm::class,
            Form\EditForm::class,
            Form\BlockEditor::class,
            Form\Panel::class,
            Form\Select::class,
            Form\ShortText::class,
            Form\Slug::class,
            Form\Textarea::class,
            Form\Toggle::class,

            // Listing components
            Listing\RowCellRenderer::class,
            Listing\Boolean::class,
            Listing\ShortText::class,
            Listing\Select::class,
            Listing\ReactiveToggle::class,
            Listing\ListingTable::class,
        ];

        foreach ($components as $component) {
            if (is_a($component, \Livewire\Component::class, true)) {
                LivewireFacade::component($component::tag(), $component);

                continue;
            }

            Blade::component($component, $component::tag());
        }
    }
}
