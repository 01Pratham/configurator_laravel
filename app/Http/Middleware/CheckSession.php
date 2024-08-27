<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    private array $excludedRoutes = [
        '/',
        'login',
        "Dashboard",
        'register',
        "test-email",
        "save-prices",
        'password'
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->check_excluded($request->path()) && (!Auth::user() || Auth::check())) {
            return redirect('/Dashboard');
        }

        return $next($request);
    }

    private function check_excluded(string $req_path): bool
    {
        foreach ($this->excludedRoutes as $path) {
            if ($path == "/") return true;
            if (preg_match("/{$path}/", $req_path)) return true;
        }
        return false;
    }
}
    