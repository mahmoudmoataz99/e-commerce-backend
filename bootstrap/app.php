<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',   // ← must be here
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
    ->withMiddleware(function (Middleware $middleware) {
        // this line is needed so sanctum can check cookies for our stateful
        // domains (mostly only matters if you test the api from a web browser).
        // for the react native app itself, we are just using bearer tokens
        // so this doesnt really get used, but doesnt hurt to have it
        // $middleware->statefulApi();

        // make sure CORS is turned on so the app (running on a different port/host)
        // is allowed to call this api
        // $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
