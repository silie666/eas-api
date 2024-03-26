<?php

namespace App\Http\Middleware;

use Closure;

class SetPassportAuthGuard
{
    public function handle($request, Closure $next, $guard = 'api')
    {
        app('config')->set('auth.passport.guard', $guard); // save current guard name in config
        return $next($request);
    }
}