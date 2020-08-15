<?php

namespace CbtechLtd\Fastlane\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('fastlane-cp')->check()) {
            return redirect($this->getGuardRedirectionPath());
        }

        return $next($request);
    }

    protected function getGuardRedirectionPath(): string
    {
        return route('cp.dashboard');
    }
}
