<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\RendersOnMenu;
use CbtechLtd\Fastlane\Fields\Support\SelectOption;
use CbtechLtd\Fastlane\Fields\Support\SelectOptionCollection;
use CbtechLtd\Fastlane\Fields\Types\ActiveToggle;
use CbtechLtd\Fastlane\Fields\Types\Panel;
use CbtechLtd\Fastlane\Fields\Types\Select;
use CbtechLtd\Fastlane\Fields\Types\ShortText;
use CbtechLtd\Fastlane\Http\Controllers\BackendUserController;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\NewAccessToken;
use Spatie\Permission\Models\Role;

class BackendUserEntryType extends EntryType implements RenderableOnMenu
{
    use RendersOnMenu;

    const PERM_MANAGE_SYSTEM_ADMINS = 'manage admins';
    const PERM_MANAGE_ACCESS_TOKENS = 'manage personal access tokens';
    const ROLE_SYSTEM_ADMIN = 'system admin';

    /** @var string */
    protected static string $key = 'users';

    /** @var string|null */
    protected static ?string $icon = 'user';

    protected static array $label = [
        'singular' => 'User',
        'plural'   => 'Users',
    ];

    /** @var string */
    protected static string $repository = BackendUserRepository::class;

    /** @var string */
    protected static string $controller = BackendUserController::class;

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            Panel::make('Profile')->withFields([
                ShortText::make('Name')->required()->listable()->sortable(),
                ShortText::make('Email')->required()->unique()->listable()->sortable()->withHelp('An unique email'),
                Select::make('Role')->withOptions(SelectOptionCollection::lazy(function () {
                    return Role::all()->map(fn(Role $role) => SelectOption::make($role->name, $role->name))->all();
                })),
            ])->withIcon('id-card'),

            Panel::make('Settings')->withFields([
                ActiveToggle::make()->listable(),
            ])->withIcon('cog'),
        ];
    }

    public static function createToken(User $user, string $name, array $abilities): NewAccessToken
    {
        Gate::authorize('createToken', $user);

        return $user->createToken($name, $abilities);
    }

    protected static function menuGroup(): string
    {
        return __('fastlane::core.menu.system_group');
    }
}
