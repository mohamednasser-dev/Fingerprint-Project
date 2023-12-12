<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class CheckAdmin
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        $jwt = (request()->hasHeader('jwt') ? request()->header('jwt') : "");
        $user = check_jwt($jwt);
        if ($user && $user->type == 'admin') {
            return $next($request);
        } else {
            return msgdata(not_authoize(), 'برجاء تسجيل الدخول كمدير', (object)[]);
        }
    }
}
