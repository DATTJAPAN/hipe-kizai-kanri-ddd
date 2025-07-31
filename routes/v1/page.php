<?php

declare(strict_types=1);

use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Organization\OrganizationUnitController;
use App\Http\Controllers\System\OrganizationController as SystemOrganizationController;
use App\Http\Controllers\System\SystemController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth:web', 'auth'])
    ->prefix('org')
    ->name('org.')
    ->group(function () {
        Route::get('/', [OrganizationController::class, 'dashboard'])->name('dashboard:get');

        // org/**/*
        Route::prefix('units')
            ->name('units.')
            ->group(function () {
                Route::get('/', [OrganizationUnitController::class, 'dashboard'])->name('dashboard:get');
                Route::get('/manage/{prefixedId?}', [OrganizationUnitController::class, 'manage'])->name('manage:get');
            });
    });

Route::middleware(['auth:system', 'auth'])
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard:get');

        // sys/orgs/**/*
        Route::prefix('orgs')
            ->name('orgs.')
            ->group(function () {
                Route::get('/', [SystemOrganizationController::class, 'dashboard'])->name('dashboard:get');
                Route::get('/manage/{prefixedId?}', [SystemOrganizationController::class, 'manage'])->name('manage:get');
            });
    });
