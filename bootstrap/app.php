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
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'billing.guard' => \App\Http\Middleware\BillingGuard::class,
            'setup.guard' => \App\Http\Middleware\SetupGuard::class,
            'super_admin.guard' => \App\Http\Middleware\SuperAdminGuard::class,
            'store_user.guard' => \App\Http\Middleware\StoreUserGuard::class,
            'fnb.guard' => \App\Http\Middleware\FnbModuleGuard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
