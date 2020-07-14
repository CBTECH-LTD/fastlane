<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes;

use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UpdateRolePipe
{
    public function handle(OnSavingHook $hook, \Closure $next)
    {
        if ($role = Arr::get($hook->data(), 'role')) {
            $hook->model()->assignRole($role);
        }

        return $next($hook);
    }
}
