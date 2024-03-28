<?php

namespace App\Http\Middleware;

use App\Models\User\Student;
use App\Models\User\Teacher;
use Closure;
use Illuminate\Http\Request;
use Package\Exceptions\Client\AuthenticationException;
use Package\Exceptions\Client\ForbiddenException;


class Rbac
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        if (\Auth::guard('student')->check()) {
            \Auth::setUser(\Auth::guard('student')->user());
        }

        $user = \Auth::user();
        if (!$user) {
            throw new AuthenticationException('未登录');
        }
        $uri = $request->getRequestUri();

        if (!\Str::startsWith($uri, '/api/common-api')) {
            if (
                ($user instanceof Student && !\Str::startsWith($uri, '/api/student-api'))
                ||
                ($user instanceof Teacher && !\Str::startsWith($uri, '/api/teacher-api'))
            ) {
                throw new ForbiddenException('无权限访问');
            }
        }

        return $next($request);
    }
}