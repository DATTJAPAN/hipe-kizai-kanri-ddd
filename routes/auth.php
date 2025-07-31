<?php

declare(strict_types=1);
//
// declare(strict_types=1);
//
// use App\Http\Controllers\System\Auth\SystemAuthController;
// use Illuminate\Support\Facades\Route;
//
// Route::middleware('guest')->group(function () {
//    Route::get('register', [RegisteredUserController::class, 'create'])
//        ->name('register');
//
//    Route::post('register', [RegisteredUserController::class, 'store']);
//
//    Route::get('login', [SystemAuthController::class, 'viewSystemSignIn'])
//        ->name('login');
// //    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
// //        ->name('password.request');
// //
// //    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
// //        ->name('password.email');
// //
// //    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
// //        ->name('password.reset');
// //
// //    Route::post('reset-password', [NewPasswordController::class, 'store'])
// //        ->name('password.store');
//
//    Route::get('system-login', [SystemAuthController::class, 'viewSystemSignIn'])
//        ->name('get.system_login');
//    Route::post('system-login', [SystemAuthController::class, 'processSystemSignIn'])
//        ->name('post.system_login');
// });
//
//
// Route::middleware(['auth:system', 'auth'])->group(function () {
//    Route::post('system-logout', [SystemAuthController::class, 'processSystemSignOut'])->name('post.system_logout');
// });
//
//
// //
// //Route::middleware('auth')->group(function () {
// //    Route::get('verify-email', EmailVerificationPromptController::class)
// //        ->name('verification.notice');
// //
// //    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
// //        ->middleware(['signed', 'throttle:6,1'])
// //        ->name('verification.verify');
// //
// //    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
// //        ->middleware('throttle:6,1')
// //        ->name('verification.send');
// //
// //    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
// //        ->name('password.confirm');
// //
// //    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
// //
// //    Route::post('logout', [SystemAuthController::class, 'destroy'])
// //        ->name('logout');
// //});
