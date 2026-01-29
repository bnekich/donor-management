<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies (needed for Digital Ocean App Platform and similar hosting)
        $middleware->trustProxies(at: '*');

        // Register alias for password change and 2FA middleware
        $middleware->alias([
            'password.change.required' => \App\Http\Middleware\RequirePasswordChange::class,
            'two-factor.required' => \App\Http\Middleware\RequireTwoFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
