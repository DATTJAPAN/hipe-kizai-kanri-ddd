<?php

declare(strict_types=1);

/**
 * Pre-applied Middleware: 'web'
 * Name Prefixed: v1.
 * URL Prefixed: /v1/
 */

use App\Http\Controllers\Organization\Auth\OrganizationAuthController;
use App\Http\Controllers\System\Auth\SystemAuthController;

// Ensure the user is not "authenticated"
Route::middleware('guest')->group(function () {
    Route::get('system-login', [SystemAuthController::class, 'getLogin'])->name('system_login:get');
    Route::post('system-login', [SystemAuthController::class, 'postLogin'])->name('system_login:post');

    Route::get('login', [OrganizationAuthController::class, 'getLogin'])->name('login:get');
    Route::post('login', [OrganizationAuthController::class, 'postLogin'])->name('login:post');

});

Route::middleware('auth')->group(function () {
    Route::post('logout', [OrganizationAuthController::class, 'postLogOut'])->name('logout:post');
});
// Ensure the user is "authenticated" and with a "system" guard
Route::middleware('auth:system')->group(function () {
    Route::post('system-logout', [SystemAuthController::class, 'postLogOut'])->name('system_logout:post');
});
