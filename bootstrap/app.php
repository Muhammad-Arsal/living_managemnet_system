<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('staff') || $request->is('staff/*')) {
                return route('staff.login');
            }

            if ($request->is('council') || $request->is('council/*')) {
                return route('council.login');
            }

            return route('admin.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('staff') || $request->is('staff/*')) {
                return route('staff.dashboard');
            }

            if ($request->is('council') || $request->is('council/*')) {
                return route('council.dashboard');
            }

            return route('admin.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
