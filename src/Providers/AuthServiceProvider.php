<?php declare(strict_types = 1);

namespace CbtechLtd\Fastlane\Providers;

use CbtechLtd\Fastlane\EntryTypes\BackendUser\BackendUserEntryType;
use CbtechLtd\Fastlane\EntryTypes\BackendUser\Model\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Implicitly grant "Super Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if (config('fastlane.super_admin') && $user->hasRole(BackendUserEntryType::ROLE_SYSTEM_ADMIN)) {
                return true;
            }
        });

        // Add Fastlane guards, providers and password recovery to
        // Authentication configuration.
        Config::set('auth.guards.fastlane-cp', [
            'driver'   => 'session',
            'provider' => 'fastlane-users',
        ]);

        Config::set('auth.guards.fastlane-api', [
            'driver'   => 'sanctum',
            'provider' => 'fastlane-users',
        ]);

        Config::set('auth.providers.fastlane-users', [
            'driver' => 'eloquent',
            'model'  => User::class,
        ]);

        Config::set('auth.passwords.fastlane-users', [
            'provider' => 'fastlane-users',
            'table'    => 'fastlane_password_resets',
            'expire'   => 60,
            'throttle' => 60,
        ]);

        // Hijack the Livewire middleware group to allow the authenticated user
        // to use reactive components.
        Config::set('livewire.middleware_group', [
            ...Arr::wrap(Config::get('livewire.middleware_group', [])),
            'auth:fastlane-cp',
        ]);
    }
}
