<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BulkEmailController;
use App\Http\Controllers\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'landing')->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Settings (accessible by all authenticated users)
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Email Verification (custom)
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('/users/{user}/send-verification', [UserController::class, 'sendVerificationEmail'])
        ->name('users.sendVerification');

    Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (only for master, superadmin, admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':master,superadmin,admin'])->group(function () {

        // Dashboard
        Route::view('/dashboard', 'dashboard')->name('dashboard');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/subscriptions', [UserController::class, 'updateSubscriptions'])
            ->name('users.updateSubscriptions');

        // Subscriptions
        Route::view('/subscriptions', 'subscriptions.index')->name('subscriptions.index');

        // Email Tools
        Route::prefix('emails')->name('emails.')->group(function () {
            Route::get('/', [EmailController::class, 'index'])->name('index');
            Route::get('/history', [BulkEmailController::class, 'index'])->name('history');
            Route::get('/progress', [EmailController::class, 'bulkProgress'])->name('progress');

            Route::get('{templateId}', [EmailController::class, 'show'])->name('show');
            Route::get('{templateId}/send', [EmailController::class, 'sendForm'])->name('sendForm');
            Route::post('{templateId}/send', [EmailController::class, 'sendBulk'])->name('sendBulk');
            Route::post('{templateId}/send-test', [EmailController::class, 'sendTest'])->name('sendTest');
        });
    });
});