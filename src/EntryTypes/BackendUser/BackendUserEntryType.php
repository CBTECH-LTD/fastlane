<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser;

use CbtechLtd\Fastlane\Contracts\RenderableOnMenu;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use CbtechLtd\Fastlane\EntryTypes\EntryType;
use CbtechLtd\Fastlane\EntryTypes\QueryBuilder;
use CbtechLtd\Fastlane\EntryTypes\RendersOnMenu;
use CbtechLtd\Fastlane\Fields\Types\ActiveToggle;
use CbtechLtd\Fastlane\Fields\Types\Panel;
use CbtechLtd\Fastlane\Fields\Types\ShortText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

class BackendUserEntryType extends EntryType implements RenderableOnMenu
{
    use RendersOnMenu;

    const PERM_MANAGE_SYSTEM_ADMINS = 'manage admins';
    const PERM_MANAGE_ACCESS_TOKENS = 'manage personal access tokens';
    const ROLE_SYSTEM_ADMIN = 'system admin';

    public static function boot(): void
    {
        parent::boot();

        static::listen(static::EVENT_CREATING, function (Model $model, array $data) {
            $model->setPasswordAttribute(Str::random(12));
        });

        static::listen(static::EVENT_QUERY_LISTING, function (QueryBuilder $builder) {
            $builder->except([Auth::user()->getKey()]);
        });
    }

    public static function model(): string
    {
        return User::class;
    }

    public static function policy(): ?string
    {
        return BackendUserPolicy::class;
    }

    public static function icon(): string
    {
        return 'user';
    }

    public static function name(): string
    {
        return __('fastlane::core.user.singular_name');
    }

    public static function pluralName(): string
    {
        return __('fastlane::core.user.plural_name');
    }

    public static function fields(): array
    {
        return [
            ShortText::make('Name')->required()->listable()->sortable(),
            ShortText::make('Email')->required()->unique()->listable()->sortable(),
            Panel::make('Settings')->withFields([
                ActiveToggle::make()->listable(),
            ]),
        ];
    }

    public function createToken(string $name, array $abilities): NewAccessToken
    {
        Gate::authorize('createToken', $this->modelInstance());

        return $this->modelInstance()->createToken($name, $abilities);
    }

    protected static function menuGroup(): string
    {
        return __('fastlane::core.menu.system_group');
    }
}
