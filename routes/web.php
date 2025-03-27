<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmailController;

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

Route::middleware(['auth'])->group(function () {

    // Dashboard & Static Pages
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/users', 'users.index')->name('users.index');
    Route::view('/subscriptions', 'subscriptions.index')->name('subscriptions.index');

    // Email Routes
    Route::prefix('emails')->name('emails.')->group(function () {
        Route::get('/', [EmailController::class, 'index'])->name('index');
        Route::get('/progress', [EmailController::class, 'bulkProgress'])->name('progress');

        Route::get('{templateId}', [EmailController::class, 'show'])->name('show');
        Route::get('{templateId}/send', [EmailController::class, 'sendForm'])->name('sendForm');
        Route::post('{templateId}/send', [EmailController::class, 'sendBulk'])->name('sendBulk');
        Route::post('{templateId}/send-test', [EmailController::class, 'sendTest'])->name('sendTest');
    });

    Route::get('/emails/history', [\App\Http\Controllers\BulkEmailController::class, 'index'])
    ->name('emails.history');
});