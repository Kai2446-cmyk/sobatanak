<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Vercel berada di belakang reverse proxy; percayai forwarded HTTPS/host headers.
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders()
    ->create();
