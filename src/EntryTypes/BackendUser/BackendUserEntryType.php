<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\RandomPasswordPipe;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\UpdateRolePipe;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use CbtechLtd\Fastlane\Support\Schema\Fields\SelectField;
use CbtechLtd\Fastlane\Support\Schema\Fields\StringField;
use CbtechLtd\Fastlane\Support\Schema\Fields\ToggleField;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
                ->hideOnForm(function () {
                    return Auth::user()->is($this->modelInstance());
                })
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

    public function updateAuthenticatedUser(Request $request): User
    {
        $entry = Auth::user();

        // Validate the request data against the update fields
        // and save the validated data in a new variable.
        $fields = $this->schema()->getUpdateFields();

        $rules = Collection::make($fields)
            ->mapWithKeys(fn(SchemaField $fieldType) => $fieldType->getUpdateRules())
            ->all();

        $data = Validator::make($request->all(), $rules)->validated();

        // Pass the validated date through all fields, call hooks before saving,
        // then call more hooks after model has been saved.
        $this->hydrateFields($entry, $fields, $data);

        $beforeHook = new OnSavingHook($this, $entry, $data);
        $this->executeHooks(static::HOOK_BEFORE_UPDATING, $beforeHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $beforeHook);

        $beforeHook->model()->save();

        $afterHook = new OnSavingHook($this, $beforeHook->model(), $data);
        $this->executeHooks(static::HOOK_AFTER_UPDATING, $afterHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $afterHook);

        return $afterHook->model();
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
