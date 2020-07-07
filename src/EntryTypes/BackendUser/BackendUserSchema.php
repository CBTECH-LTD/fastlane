<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\Support\Schema\EntrySchema;
use CbtechLtd\Fastlane\Support\Schema\EntrySchemaDefinition;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Config\SingleChoiceOption;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\Constraints\Unique;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\SingleChoiceType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\StringType;
use CbtechLtd\Fastlane\Support\Schema\FieldTypes\ToggleType;
use Spatie\Permission\Models\Role;

class BackendUserSchema extends EntrySchema
{
    public function build(): EntrySchemaDefinition
    {
        return EntrySchemaDefinition::make([
            StringType::make('name', 'Name')
                ->required()
                ->setRules('max:255')
                ->showOnIndex(),

            StringType::make('email', 'Email')
                ->required()
                ->unique(new Unique(User::class, 'email'))
                ->setRules('max:255')
                ->showOnIndex(),

            SingleChoiceType::make('role', 'Role')
                ->setOptions(
                    Role::all()->map(
                        fn(Role $role) => SingleChoiceOption::make($role->name, $role->name)
                    )->all()
                )
                ->required()
                ->showOnIndex(),

            ToggleType::make('is_active', 'Active')
                ->required(),
        ]);
    }
}
