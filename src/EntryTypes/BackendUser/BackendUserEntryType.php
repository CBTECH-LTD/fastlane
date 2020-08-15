<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\RandomPasswordPipe;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\UpdateRolePipe;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use CbtechLtd\Fastlane\Support\Schema\Fields\SelectField;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\NewAccessToken;
use Spatie\Permission\Models\Role;

class BackendUserEntryType extends EntryType
{
    const PERM_MANAGE_SYSTEM_ADMINS = 'manage admins';
    const PERM_MANAGE_ACCESS_TOKENS = 'manage personal access tokens';
    const ROLE_SYSTEM_ADMIN = 'system admin';

    public function __construct(Gate $gate)
    {
        parent::__construct($gate);

        $this->addHook(static::HOOK_BEFORE_CREATING, RandomPasswordPipe::class);
        $this->addHook(static::HOOK_BEFORE_UPDATING, UpdateRolePipe::class);
    }

    public function icon(): string
    {
        return 'user';
    }

    public function model(): string
    {
        return User::class;
    }

    public function fields(): array
    {
        return [
            StringField::make('name', 'Name')
                ->required()
                ->setRules('max:255')
                ->showOnIndex(),

            StringField::make('email', 'Email')
                ->required()
                ->unique(new Unique(User::class, 'email'))
                ->setRules('max:255')
                ->showOnIndex(),

            SelectField::make('role', 'Role')
                ->withOptions(
                    Role::all()->map(
                        fn(Role $role) => SelectOption::make($role->name, $role->name)
                    )->all()
                )
                ->required()
                ->showOnIndex()
                ->fillModelUsing(function ($model, $value) {
                    if ($value) {
                        $model->assignRole($value);
                    }
                }),

            ToggleField::make('is_active', 'Active')
                ->required()
                ->setDefault(true),
        ];
    }

    public function createToken(string $userHashid, string $name, array $abilities): NewAccessToken
    {
        /** @var User $user */
        $user = $this->newModelInstance()
            ->newModelQuery()
            ->whereHashid($userHashid)
            ->firstOrFail();

        $this->gate->authorize('createToken', $user);

        return $user->createToken($name, $abilities);
    }

    protected function queryItems(Builder $query): void
    {
        $query->except(Auth::user());
    }

    protected function querySingleItem(Builder $query, string $hashid): void
    {
        $query->except(Auth::user());
    }
}
