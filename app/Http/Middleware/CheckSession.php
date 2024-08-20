<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $excludedRoutes = [
            '/',
            'login',
            "Dashboard",
            'register',
            'password/reset',
            'password/email',
            'password/reset/*',
            'password/confirm',
        ];

        if (!in_array($request->path(), $excludedRoutes) && !Auth::user()) {
            return redirect('/Dashboard');
        }

        return $next($request);
    }
}
