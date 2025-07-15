<?php

declare(strict_types=1);

/**
 * Pre-applied Middleware: web | xhr_request_only
 * Name Prefixed: v1.req.
 * URL Prefixed: /v1/req/
 */

// Ensure the user is not "authenticated"
Route::middleware('guest')->group(function () {});

// Ensure the user is "authenticated" and with a "web" guard
Route::middleware('auth')->group(function () {});

// Ensure the user is "authenticated" and with a "system" guard
Route::middleware('auth:system')
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {

        Route::prefix('org')
            ->name('org.')
            ->group(function () {
                // TODO:
            });

    });
