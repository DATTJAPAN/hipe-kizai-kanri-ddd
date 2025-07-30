<?php

declare(strict_types=1);

/**
 * Pre-applied Middleware: web | xhr_request_only
 * Name Prefixed: v1.req.
 * URL Prefixed: /v1/req/
 */

// Ensure the user is not "authenticated"
use App\Domains\Organization\Units\OrganizationUnitController;
use App\Domains\System\Organizations\OrganizationController;

Route::middleware('guest')->group(function () {
});

// Ensure the user is "authenticated" and with a "web" guard
Route::middleware('auth')
    ->prefix('org')
    ->name('org.')
    ->group(function () {
        Route::prefix('units')
            ->name('units.')
            ->group(function () {
                Route::post('/', [OrganizationUnitController::class, 'datatable'])
                    ->name('datatable:post');
            });
    });

// Ensure the user is "authenticated" and with a "system" guard
Route::middleware('auth:system')
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        Route::prefix('orgs')
            ->name('orgs.')
            ->group(function () {
                Route::post('/', [OrganizationController::class, 'datatable'])
                    ->name('datatable:post');
            });
    });
