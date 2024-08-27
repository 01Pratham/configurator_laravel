<?php

namespace App\Http\Middleware;

use App\Models\VisitorActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreActivityLogs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = !empty(session()->get('user')) ? session()->get('user') : [
            "crm_user_id" => null,
            "username" => null
        ];
        VisitorActivityLog::create([
            "session_id" => session()->getId(),
            "user_ip_address" => $request->ip(),
            "emp_code" => $user["crm_user_id"],
            "uname" => $user["username"],
            "page_url" => $request->url(),
        ]);
        return $next($request);
    }
}
