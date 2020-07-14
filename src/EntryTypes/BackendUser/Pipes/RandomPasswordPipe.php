<?php

namespace CbtechLtd\Fastlane\EntryTypes\BackendUser\Pipes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RandomPasswordPipe
{
    public function handle(Model $model, array $data, \Closure $next)
    {
        $model->password = Str::random(16);

        return $next($model, $data);
    }
}
