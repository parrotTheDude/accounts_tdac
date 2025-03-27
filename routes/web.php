<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'landing')->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification email sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard & Static Pages
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/users', 'users.index')->name('users.index');
    Route::view('/subscriptions', 'subscriptions.index')->name('subscriptions.index');

    Route::prefix('emails')->name('emails.')->group(function () {
        Route::get('/', [EmailController::class, 'index'])->name('index');
        Route::get('/history', [\App\Http\Controllers\BulkEmailController::class, 'index'])->name('history');
        Route::get('/progress', [EmailController::class, 'bulkProgress'])->name('progress');
    
        // ⚠️ Catch-all must come last
        Route::get('{templateId}', [EmailController::class, 'show'])->name('show');
        Route::get('{templateId}/send', [EmailController::class, 'sendForm'])->name('sendForm');
        Route::post('{templateId}/send', [EmailController::class, 'sendBulk'])->name('sendBulk');
        Route::post('{templateId}/send-test', [EmailController::class, 'sendTest'])->name('sendTest');
    });

    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
});