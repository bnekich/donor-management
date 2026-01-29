<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\RedirectsIfTwoFactorAuthenticatable;

class RedirectIfTwoFactorAuthenticatable implements RedirectsIfTwoFactorAuthenticatable
{
    /**
     * In non-production, skip the 2FA challenge. In production, delegate to Fortify's default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        if (! app()->environment('production')) {
            return $next($request);
        }

        return app(\Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable::class)->handle($request, $next);
    }
}
