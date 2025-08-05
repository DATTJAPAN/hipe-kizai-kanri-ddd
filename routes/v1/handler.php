<?php

declare(strict_types=1);

use App\Http\Controllers\Organization\OrganizationController;
use App\Http\Controllers\Organization\OrganizationLocationController;
use App\Http\Controllers\Organization\OrganizationNetworkHostController;
use App\Http\Controllers\Organization\OrganizationTagController;
use App\Http\Controllers\Organization\OrganizationUnitController;
use App\Http\Controllers\System\OrganizationController as SystemOrganizationController;
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

        Route::prefix('locations')
            ->name('locations.')
            ->group(function () {
                Route::post('add', [OrganizationLocationController::class, 'addHandler'])->name('add:post');
                Route::put('update/{prefixedId}', [OrganizationLocationController::class, 'updateHandler'])->name('update:put');
                Route::delete('delete/{prefixedId}', [OrganizationLocationController::class, 'forceDeleteHandler'])->name('delete:delete');
            });

        Route::prefix('network_hosts')
            ->name('network_hosts.')
            ->group(function () {
                Route::post('add', [OrganizationNetworkHostController::class, 'addHandler'])->name('add:post');
                Route::put('update/{prefixedId}', [OrganizationNetworkHostController::class, 'updateHandler'])->name('update:put');
                Route::delete('delete/{prefixedId}', [OrganizationNetworkHostController::class, 'forceDeleteHandler'])->name('delete:delete');
            });

        Route::prefix('tags')
            ->name('tags.')
            ->group(function () {
                Route::post('add', [OrganizationTagController::class, 'addHandler'])->name('add:post');
                Route::put('update/{prefixedId}', [OrganizationTagController::class, 'updateHandler'])->name('update:put');
                Route::delete('delete/{prefixedId}', [OrganizationTagController::class, 'forceDeleteHandler'])->name('delete:delete');
            });

        Route::prefix('units')
            ->name('units.')
            ->group(function () {
                Route::post('add', [OrganizationUnitController::class, 'addHandler'])->name('add:post');
                Route::put('update/{prefixedId}', [OrganizationUnitController::class, 'updateHandler'])->name('update:put');
                Route::delete('delete/{prefixedId}', [OrganizationUnitController::class, 'forceDeleteHandler'])->name('delete:delete');
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
