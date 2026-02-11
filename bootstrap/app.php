<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

// Mengkonfigurasi dan membuat instance utama aplikasi Laravel
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php', // Mendefinisikan file routing untuk web
        commands: __DIR__.'/../routes/console.php', // Mendefinisikan file routing untuk console/artisan
        health: '/up', // Endpoint untuk health check aplikasi
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Mendaftarkan alias middleware 'role' untuk RoleMiddleware
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tempat konfigurasi custom exception handling (jika diperlukan)
        //
    })
    ->create(); // Membuat dan mengembalikan instance aplikasi
