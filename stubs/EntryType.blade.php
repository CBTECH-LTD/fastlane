namespace {{ $namespace }};

use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\StringType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\ToggleType;

class {{ $class }} extends EntryType
{
    public function schema(): array
    {
        return [
            StringType::make('name', 'Name')
                ->required()
                ->setRules('max:255')
                ->showOnIndex(),

            ToggleType::make('is_active', 'Active')
                ->required(),
        ];
    }
}
