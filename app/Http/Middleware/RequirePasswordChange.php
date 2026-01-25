<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->mustChangePassword()) {
            // Allow access to password change route and logout
            if (! $request->routeIs('password.change') && ! $request->routeIs('logout')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}
