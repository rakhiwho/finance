<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

    $middleware->statefulApi();    
    // to check autorization  of roles
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);

})
->withExceptions(function (Exceptions $exceptions) {

    // Forcing JSON response for all API requests (this fixes most "login route" issues)
    $exceptions->shouldRenderJsonWhen(function (Request $request) {
        return $request->is('api/*') 
            || $request->wantsJson() 
            || $request->expectsJson();
    });

})->create();