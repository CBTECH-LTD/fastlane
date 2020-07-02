<?php

use App\Http\ControlPanel\Controllers;
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
    $router->get('dashboard', [\CbtechLtd\Fastlane\Http\Controllers\DashboardController::class, 'show'])->name('dashboard');

    // Backend users
//    $router->get('backend-users', [Controllers\BackendUsersController::class, 'index'])->name('backend-users.index');
//    $router->get('backend-users/new', [Controllers\BackendUsersController::class, 'create'])->name('backend-users.create');
//    $router->post('backend-users', [Controllers\BackendUsersController::class, 'store'])->name('backend-users.store');
//    $router->get('backend-users/{user}', [Controllers\BackendUsersController::class, 'edit'])->name('backend-users.edit');
//    $router->patch('backend-users/{user}', [Controllers\BackendUsersController::class, 'update']);
//    $router->delete('backend-users/{user}', [Controllers\BackendUsersController::class, 'delete']);

    // Register Content Types routes
    \CbtechLtd\Fastlane\FastlaneFacade::registerRoutes($router);
});
