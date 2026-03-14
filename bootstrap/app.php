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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Valida rol(es): ->middleware('role:admin') o ->middleware('role:admin,manager')
            'role'   => \App\Http\Middleware\CheckRole::class,

            // Valida rol(es) + cuenta activa + email verificado en un solo middleware
            'ensure.role' => \App\Http\Middleware\EnsureRole::class,

            // Solo valida que la cuenta este activa (usar tras auth:sanctum)
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();