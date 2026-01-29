<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     * In production, redirect to 2FA setup if the user has not confirmed two-factor authentication.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('production')) {
            return $next($request);
        }

        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->two_factor_confirmed_at !== null) {
            return $next($request);
        }

        if ($this->isTwoFactorRoute($request)) {
            return $next($request);
        }

        if ($request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('two-factor.show');
    }

    /**
     * Determine if the request is for a two-factor related route.
     */
    private function isTwoFactorRoute(Request $request): bool
    {
        return $request->routeIs('two-factor.*') || $request->routeIs('password.confirm*');
    }
}
