<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmailController;

Route::get('/', fn() => view('landing'))->name('home'); // or your custom landing
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard'); // create this view next
})->middleware('auth');

Route::get('/users', function () {
    return view('users.index');
})->middleware('auth');

Route::get('/subscriptions', function () {
    return view('subscriptions.index');
})->middleware('auth');

Route::get('/emails', function () {
    return view('emails.index');
})->middleware('auth');

Route::get('/emails', [EmailController::class, 'index'])->middleware('auth');
Route::post('/emails/send/{templateId}', [EmailController::class, 'send'])->middleware('auth');
Route::get('/emails/{templateId}', [EmailController::class, 'show'])->name('emails.show')->middleware('auth');
Route::post('/emails/{templateId}/send-test', [EmailController::class, 'sendTest'])->name('emails.sendTest');
Route::get('/emails/{templateId}/send', [EmailController::class, 'sendForm'])->name('emails.sendForm');
Route::post('/emails/{templateId}/send', [EmailController::class, 'sendBulk'])->name('emails.sendBulk');