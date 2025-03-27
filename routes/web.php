<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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