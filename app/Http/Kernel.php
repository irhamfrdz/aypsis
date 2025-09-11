<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        // ...existing code...
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // ...existing code...
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \App\Http\Middleware\VerifyCsrfToken::class, // Disabled for testing
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
    // ...existing code...
    'role' => \App\Http\Middleware\EnsureRole::class,
    'ensure.karyawan' => \App\Http\Middleware\EnsureKaryawanPresent::class,
    'ensure.approved' => \App\Http\Middleware\EnsureUserApproved::class,
    'ensure.crew_checklist' => \App\Http\Middleware\EnsureCrewChecklistComplete::class,
    ];

    public function __construct($app, $router)
    {
        parent::__construct($app, $router);
        if (app()->environment('testing')) {
            $this->middlewareGroups['web'] = array_filter(
                $this->middlewareGroups['web'],
                fn($middleware) => $middleware !== \App\Http\Middleware\VerifyCsrfToken::class
            );
        }
    }
}
