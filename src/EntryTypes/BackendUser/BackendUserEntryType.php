<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\RandomPasswordPipe;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes\UpdateRolePipe;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\Support\Concerns\RendersOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\EntryInstance;
use CbtechLtd\Fastlane\Support\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Config\SelectOption;
use CbtechLtd\Fastlane\Support\Schema\Fields\Constraints\Unique;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\WithRules;
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

class BackendUserEntryType extends EntryType implements RenderableOnMenu
{
    use RendersOnMenu;

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

    public function name(): string
    {
        return __('fastlane::core.user.singular_name');
    }

    public function pluralName(): string
    {
        return __('fastlane::core.user.plural_name');
    }

    public function model(): string
    {
        return User::class;
    }

    public function fields(): array
    {
        return [
            StringField::make('name', __('fastlane::core.fields.name'))
                ->required()
                ->showOnIndex(),

            StringField::make('email', __('fastlane::core.fields.email'))
                ->required()
                ->unique()
                ->showOnIndex(),

            SelectField::make('role', __('fastlane::core.fields.role'))
                ->withOptions(
                    Role::all()->map(
                        fn(Role $role) => SelectOption::make($role->name, $role->name)
                    )->all()
                )
                ->required()
                ->showOnIndex()
                ->hideOnForm(function (EntryInstance $entryInstance) {
                    return Auth::user()->is($entryInstance->model());
                })
                ->writeValueUsing(function (EntryInstance $entryInstance, $value) {
                    if ($value) {
                        $entryInstance->model()->assignRole($value);
                    }
                }),

            ToggleField::make('is_active', __('fastlane::core.fields.active'))
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

    public function updateAuthenticatedUser(Request $request): EntryInstance
    {
        $entryInstance = $this->newInstance(Auth::user());

        // Validate the request data against the update fields
        // and save the validated data in a new variable.
        $fields = $entryInstance->schema()->getUpdateFields();

        $rules = Collection::make($fields)
            ->filter(fn(SchemaField $f) => $f instanceof WithRules)
            ->mapWithKeys(fn(SchemaField $fieldType) => $fieldType->getUpdateRules())
            ->all();

        $data = Validator::make($request->all(), $rules)->validated();

        // Pass the validated date through all fields, call hooks before saving,
        // then call more hooks after model has been saved.
        $this->hydrateFields($entryInstance, $fields, $data);

        $savingHook = new OnSavingHook($entryInstance, $data);
        $this->executeHooks(static::HOOK_BEFORE_UPDATING, $savingHook);
        $this->executeHooks(static::HOOK_BEFORE_SAVING, $savingHook);

        $savingHook->entryInstance()->saveModel();

        $this->executeHooks(static::HOOK_AFTER_UPDATING, $savingHook);
        $this->executeHooks(static::HOOK_AFTER_SAVING, $savingHook);

        return $savingHook->entryInstance();
    }

    protected function queryItems(QueryBuilder $query): void
    {
        $query->except([Auth::user()->getKey()]);
    }

    protected function querySingleItem(QueryBuilder $query, string $hashid): void
    {
        $query->except([Auth::user()->getKey()]);
    }

    protected function menuGroup(): string
    {
        return __('fastlane::core.menu.system_group');
    }
}
