<?php

declare(strict_types=1);

use App\Domains\Organization\Organizations\OrganizationController;
use App\Domains\Organization\Units\OrganizationUnitController;
use App\Domains\System\Organizations\OrganizationController as SystemOrganizationController;
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
        // org/units/**/*
        Route::prefix('units')
            ->name('units.')
            ->group(function () {
                Route::post('add', [OrganizationUnitController::class, 'addHandler'])->name('add:post');
                Route::put('/update/{prefixedId}', [OrganizationUnitController::class, 'updateHandler'])->name('update:put');
                Route::delete('/delete/{prefixedId}', [OrganizationUnitController::class, 'softDeleteHandler'])->name('delete:delete');
                Route::patch('/restore/{prefixedId}', [OrganizationUnitController::class, 'restoreHandler'])->name('restore:patch');
            });
    });

Route::middleware(['auth:system', 'auth'])
    ->prefix('sys')
    ->name('sys.')
    ->group(function () {
        // sys/orgs/**/*
        Route::prefix('orgs')
            ->name('orgs.')
            ->group(function () {
                Route::post('/add', [SystemOrganizationController::class, 'addHandler'])->name('add:post');
                Route::put('/update/{prefixedId}', [SystemOrganizationController::class, 'updateHandler'])->name('update:put');
                Route::delete('/delete/{prefixedId}', [SystemOrganizationController::class, 'softDeleteHandler'])->name('soft_delete:delete');
                Route::delete('/force-delete/{prefixedId}', [SystemOrganizationController::class, 'forceDeleteHandler'])->name('force_delete:delete');
                Route::patch('/restore/{prefixedId}', [SystemOrganizationController::class, 'restoreHandler'])->name('restore:patch');
            });
    });
