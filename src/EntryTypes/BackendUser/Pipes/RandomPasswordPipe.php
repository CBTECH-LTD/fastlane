<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes;

use CbtechLtd\Fastlane\EntryTypes\Hooks\OnSavingHook;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RandomPasswordPipe
{
    public function handle(OnSavingHook $hook, Closure $next)
    {
        $hook->model()->password = Str::random(16);

        return $next($hook);
    }
}
