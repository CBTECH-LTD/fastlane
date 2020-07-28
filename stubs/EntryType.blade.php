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

            ToggleField::make('is_active', 'Active')
                ->required(),
        ];
    }
}
