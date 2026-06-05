<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackUserActivity;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Spatie permission middleware
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'set.locale' => SetLocale::class,
            'track.activity' => TrackUserActivity::class,
        ]);

	    $middleware->validateCsrfTokens(except: [
            '/pusher/auth',
        ]);
    })
    ->withEvents(discover: [
        __DIR__.'/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();