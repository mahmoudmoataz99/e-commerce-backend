<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// This file wires the app together: which route files to load,
// which middleware runs on every request, and how errors are handled.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Nothing extra needed here for now.
        //
        // Notes for future me:
        // - CORS (letting the app call this API from a different host/port) is
        //   already turned on by default in Laravel 11, no need to add it here.
        // - The "auth" and "auth:sanctum" middleware used in routes/api.php are
        //   also already registered by Laravel out of the box, no Kernel.php
        //   file is needed for that anymore (that was how it worked pre-11).
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
