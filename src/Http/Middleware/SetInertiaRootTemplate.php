<?php

namespace CbtechLtd\Fastlane\Http\Middleware;

use Closure;
use Inertia\Inertia;

class SetInertiaRootTemplate
{
    /**
     * Handle an incoming request.a
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param null                     $namespace
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next, $namespace = null)
    {
        if (! in_array($namespace, ['fastlane', 'site'])) {
            throw new \Exception('Inertia root namespace must be "fastlane" or "site"');
        }

        Inertia::setRootView("{$namespace}::app");

        return $next($request);
    }
}
