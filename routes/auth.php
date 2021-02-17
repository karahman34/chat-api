<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/me', [AuthenticatedSessionController::class, 'me'])
                ->name('me');

        Route::get('/refresh', [AuthenticatedSessionController::class, 'refresh'])
                ->name('refresh');
        
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
    });

    Route::middleware('guest')->group(function () {
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login');
            
        Route::post('/register', [RegisteredUserController::class, 'store'])
                ->name('register');

        Route::get('/verify-email/{user}', [VerifyEmailController::class, 'verify'])
                ->middleware(['signed'])
                ->name('verify-email');

        Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])
                ->name('forgot-password');

        Route::post('/reset-password', [ResetPasswordController::class, 'update'])
                ->name('reset-password');
    });
});
