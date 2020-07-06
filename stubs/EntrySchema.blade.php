namespace {{ $namespace }};

use CbtechLtd\Fastlane\Support\Schema\EntrySchema;
use CbtechLtd\Fastlane\Support\Schema\EntrySchemaDefinition;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\StringType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\ToggleType;

class {{ $class }} extends EntrySchema
{
    public function build(): EntrySchemaDefinition
    {
        return EntrySchemaDefinition::make([
            StringType::make('name', 'Name')
                ->required()
                ->setRules('max:255')
                ->showOnIndex(),

            ToggleType::make('is_active', 'Active')
            ->required(),
        ]);
    }
}
