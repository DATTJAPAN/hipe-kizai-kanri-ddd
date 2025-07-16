<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');


Route::get('sys-admin', static function () {
    return Inertia::render('dashboard');
})->name('dashboard');

Route::middleware(['auth:system', 'auth', 'verified'])
    ->group(function () {

});
