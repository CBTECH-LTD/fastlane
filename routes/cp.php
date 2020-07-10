<?php

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');

// Authentication Routes...
Route::get('login', [\CbtechLtd\Fastlane\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [\CbtechLtd\Fastlane\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [\CbtechLtd\Fastlane\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Password Reset Routes...
Route::get('password/reset', [\CbtechLtd\Fastlane\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [\CbtechLtd\Fastlane\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [\CbtechLtd\Fastlane\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [\CbtechLtd\Fastlane\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Password Confirmation Routes...
Route::get('password/confirm', [\CbtechLtd\Fastlane\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [\CbtechLtd\Fastlane\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm']);

// Email Verification Routes...
Route::get('email/verify', [\CbtechLtd\Fastlane\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [\CbtechLtd\Fastlane\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [\CbtechLtd\Fastlane\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');

/*
|--------------------------------------------------------------------------
| Private Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:cp', 'verified'])->group(function ($router) {
    $router->get('dashboard', [DashboardController::class, 'show'])->name('dashboard');

    // Register Content Types routes
    FastlaneFacade::registerRoutes($router);
});
