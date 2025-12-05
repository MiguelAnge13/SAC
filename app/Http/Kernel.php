<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Manejo de proxies (trusted proxies)
        \App\Http\Middleware\TrustProxies::class,

        // Manejo de CORS (fruitcake/laravel-cors)
        \Illuminate\Http\Middleware\HandleCors::class,

        // Prevención de requests durante mantenimiento
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validación del tamaño máximo de POST
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        // Trim strings
        \App\Http\Middleware\TrimStrings::class,

        // Convert empty strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,

            // Compartir errores de sesión con las vistas
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // Protección CSRF
            \App\Http\Middleware\VerifyCsrfToken::class,

            // Enlazar parámetros de ruta
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Throttle por defecto para la API
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // Middlewares estándar de Laravel
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Middlewares personalizados para la API de microcontroladores
        'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
        'api.key.admin' => \App\Http\Middleware\ApiAdminKeyMiddleware::class,
    ];
}
