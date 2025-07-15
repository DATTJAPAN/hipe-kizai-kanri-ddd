<?php

declare(strict_types=1);

/**
 * Pre-applied Middleware: 'web'
 * Name Prefixed: v1.
 * URL Prefixed: /v1/
 */

use App\Domains\System\Auth\SystemAuthController;

// Ensure the user is not "authenticated"
Route::middleware('guest')->group(function () {
    Route::get('system-login', [SystemAuthController::class, 'processSystemSignIn'])->name('system-login:get');
});

// Ensure the user is "authenticated" and with a "system" guard
Route::middleware('auth:system')->group(function () {
    Route::post('system-logout', [SystemAuthController::class, 'processSystemSignOut'])->name('system-logout:post');
});
