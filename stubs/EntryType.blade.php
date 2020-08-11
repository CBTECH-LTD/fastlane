namespace {{ $namespace }};

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;

class {{ $class }} extends EntryType
{
    public function fields(): array
    {
        return [
            StringField::make('name', 'Name')
                ->required()
                ->setRules('max:255')
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
