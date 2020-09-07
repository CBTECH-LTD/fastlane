<?php

use CbtechLtd\Fastlane\EntryTypes\FileManager\FileManagerEntryType;
use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Http\Controllers\Account\AccountProfileController;
use CbtechLtd\Fastlane\Http\Controllers\Account\AccountSecurityController;
use CbtechLtd\Fastlane\Http\Controllers\Account\AccountTokensController;
use CbtechLtd\Fastlane\Http\Controllers\API\FileManagerController;
use CbtechLtd\Fastlane\Http\Controllers\Auth\ConfirmPasswordController;
use CbtechLtd\Fastlane\Http\Controllers\Auth\ForgotPasswordController;
use CbtechLtd\Fastlane\Http\Controllers\Auth\LoginController;
use CbtechLtd\Fastlane\Http\Controllers\Auth\ResetPasswordController;
use CbtechLtd\Fastlane\Http\Controllers\Auth\VerificationController;
use CbtechLtd\Fastlane\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', config('fastlane.control_panel.url_prefix') . '/dashboard');

// Authentication Routes...
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes...
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Password Confirmation Routes...
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

// Email Verification Routes...
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

/*
|--------------------------------------------------------------------------
| Private Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['fastlane.auth:fastlane-cp', 'verified'])->group(function ($router) {
    $router->get('dashboard', [DashboardController::class, 'show'])->name('dashboard');

    $router->get('account', function () {
        return redirect()->route('cp.account.profile');
    })->name('account');
    $router->get('account/profile', [AccountProfileController::class, 'edit'])->name('account.profile');
    $router->patch('account/profile', [AccountProfileController::class, 'update']);
    $router->get('account/security', [AccountSecurityController::class, 'edit'])->name('account.security')->middleware('password.confirm:cp.password.confirm');
    $router->patch('account/security', [AccountSecurityController::class, 'update']);

    $router->get('account/tokens', [AccountTokensController::class, 'index'])->name('account.tokens.index');
    $router->get('account/tokens/new', [AccountTokensController::class, 'create'])->name('account.tokens.create')->middleware('password.confirm:cp.password.confirm');
    $router->post('account/tokens', [AccountTokensController::class, 'store'])->name('account.tokens.store');
    $router->delete('account/tokens/{token}', [AccountTokensController::class, 'destroy'])->name('account.tokens.destroy');

    $router->middleware('fastlane.resolve:' . app()->make(FileManagerEntryType::class)->identifier())->group(function () use ($router) {
        $router->get('file-manager/files', [FileManagerController::class, 'index'])->name('file-manager.index');
        $router->post('file-manager/files', [FileManagerController::class, 'store'])->name('file-manager.store');
    });

    // Register Entry Types routes
    FastlaneFacade::registerControlPanelRoutes($router);
});
