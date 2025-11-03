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
        
        // 🛑 CORRECTION : Suppression de l'alias 'auth' non nécessaire 🛑
        $middleware->alias([
            
            // Votre alias pour la vérification des droits d'administrateur
            'admin' => \App\Http\Middleware\AdminMiddleware::class, 
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();