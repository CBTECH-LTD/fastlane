namespace {{ $namespace }};

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;
use CbtechLtd\Fastlane\Support\Concerns\RendersOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\RenderableOnMenu;

class {{ $class }} extends EntryType implements RenderableOnMenu
{
    use RendersOnMenu;

    public function fields(): array
    {
        return [
            StringField::make('name', 'Name')
                ->required()
                ->showOnIndex(),

            FieldPanel::make('Related Content')->withIcon('project-diagram')
                ->withFields([
                    // Related entry types...
                ]),

            FieldPanel::make('Settings')->withIcon('tools')
                ->withFields([
                    ToggleField::make('is_active', 'Active')
                        ->required()
                        ->showOnIndex(),
                ]),
        ];
    }
}
