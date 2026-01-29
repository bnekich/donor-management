<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manageUsers', fn (User $user) => $user->is_admin);

        // Force HTTPS URLs in production or when FORCE_HTTPS is enabled
        // This is important for apps behind proxies (like Digital Ocean App Platform)
        if (App::environment('production') || config('app.force_https', false)) {
            URL::forceScheme('https');
        }

        // Automatically log in the development user if we are in local environment
        if (App::environment('local') && php_sapi_name() !== 'cli') {
            // Find the seeded user (e.g., by email)
            $user = User::where('email', 'dev@example.com')->first();

            if ($user && ! Auth::check()) {
                Auth::login($user);
            }
        }
    }
}
