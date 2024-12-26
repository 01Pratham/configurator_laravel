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
        'login',
        "Dashboard",
        'register',
        "test-email",
        "save-prices",
        'password',
        "\/Save\/Estimate\/"
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Skip session checks for excluded routes
        if ($this->check_excluded($request->path())) {

            return $next($request);
        }

        // Check if the session key "user" exists and has "crm_user_id"
        if (!session()->has('user') || !isset(session()->get('user')['crm_user_id'])) {
            return redirect('/login'); // Redirect to login or any other desired route
        }

        return $next($request);
    }


    private function check_excluded(string $req_path)
    {
        $is_excluded = false;
        foreach ($this->excludedRoutes as $path) {
            if ($req_path == "/") {
                $is_excluded = true;
                break;
            }
            if (preg_match("/{$path}/", $req_path)) {
                $is_excluded = true;
            }
        }

        // return $is_excluded;
        return true;
    }
}
