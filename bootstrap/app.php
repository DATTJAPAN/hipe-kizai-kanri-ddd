<?php

declare(strict_types=1);

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\XmlHttpRequestOnlyMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Inherit nametag and url prefix "v1"
            Route::prefix('v1')
                ->name('v1.')
                ->middleware('web')
                ->group(function () {
                    // Register 'request' with 'xhr_request_only' middleware
                    // --> additional middleware will be added inside the file
                    Route::prefix('req')
                        ->name('req.')
                        ->middleware('xhr_request_only')
                        ->group(base_path('routes/v1/request.php'));

                    // No additional pre-middleware, only inherit 'web'
                    // --> additional middleware can be added
                    require_once base_path('routes/v1/auth.php');
                    require_once base_path('routes/v1/page.php');
                    require_once base_path('routes/v1/handler.php');
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->alias([
            'xhr_request_only' => XmlHttpRequestOnlyMiddleware::class,
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware
            ->redirectUsersTo(function () {
                return 'system' === activeGuard()
                    ? '/sys/'
                    : route('v1.org.dashboard:get');
            })
            ->redirectGuestsTo(function () {
                // if they try to access 'auth:*' or 'auth' URL
                return 'system' === activeGuard()
                    ? route('v1.system_login:get')
                    : route('v1.login:get');
            });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
